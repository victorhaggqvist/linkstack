/**
 * @author Victor Häggqvist
 * @since 3/1/16
 */

var React = require('react');
import {LoadingInput} from './LoadingInput.jsx';
import {log} from '../log';

let METASERVICE = 'https://tool.stack.snilius.com';
if (BUILD_DEV) METASERVICE = 'https://rock-sorter-825.appspot.com/get?url=https://tool.stack.snilius.com';

export class PushForm extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            url: '',
            title: '',
            tags: [],
            tagsValue: '',
            status: null,
            isLoadingInfo: false,
            isPushing: false,
            buttonText: 'Push'
        }
    }

    onUrlChange(event) {
        this.setState({url: event.target.value}, () => {
            if (!this.state.isLoadingInfo) {
                if (this.state.url.length < 5) return;

                // don't refetch info
                if (!(this.state.title.length < 1 || this.state.tags.length < 1)) return;

                this.setState({isLoadingInfo: true}, () => {
                    var apiUrl = METASERVICE + '/info?url=' + this.state.url;
                    fetch(apiUrl)
                        .then(resp => resp.json(),
                            err => {
                                this.setState({isLoadingInfo: false});
                                log.debug('meta fetch fail');
                                log.debug(err);
                            })
                        .then(ret => {
                            if (ret === undefined) return;
                            this.setState({isLoadingInfo: false});

                            if (this.state.title.length < 1) this.setState({title: ret.title});

                            if (this.state.tags.length < 1) this.setState({tags: ret.meta});

                            if (this.state.url !== ret.longurl)  this.setState({url: ret.longurl});

                            this.setState({tagsValue: this.state.tags.reduce((a, b) => a == null ? b: a+', '+b, null)})
                        });
                });
            }
        });
    }

    onTagsChange(event) {
        this.setState({tagsValue: event.target.value});
        let tags = event.target.value.split(',');
        this.setState({tags: tags.map(t => t.trim()).filter(t => t.length > 0)});
    }

    push() {
        const body = {
            title: this.state.title,
            url: this.state.url,
            tags: this.state.tags
        };
        this.setState({buttonText: 'Pushing...', isPushing: true});
        fetch('/api/items', {
            method: 'post',
            body: JSON.stringify(body),
            credentials: 'include'
        })
            .then(resp => resp.json(), err => {
                cosole.log(err);
                this.setState({buttonText: 'Push', isPushing: false});
            })
            .then(json => {
                if ('message' in json) {
                    this.setState({status: json.message});
                } else {
                    this.setState({status: null, title: '', tagsValue: '', url: ''});
                    log.info(json);
                }
                this.setState({buttonText: 'Push', isPushing: false});
                this.props.onPushed();
            },err => {
                log.error(err)
            });
    }

    render() {
        let status = null;
        if (this.state.status !== null) {
            status = <div className="alert alert-info" role="alert" id="status">
                <button type="button" className="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {this.state.status}
            </div>;
        }

        return <form className="form-horizontal">
            {status}
            <div className="form-group">
                <label className="control-label col-lg-2 required" htmlFor="title">Title</label>
                <div className="col-lg-10">
                    <input type="text" id="title"
                           value={this.state.title}
                           onChange={e => this.setState({title: e.target.value})}
                           required="required" autoComplete="off" className="form-control"/>
                </div>
            </div>
            <LoadingInput id="url" label="Url" value={this.state.url}
                          loading={this.state.isLoadingInfo}
                          onChange={this.onUrlChange.bind(this)} />
            <div className="form-group">
                <label className="control-label col-lg-2" htmlFor="tags">Tags</label>
                <div className="col-lg-10">
                    <input type="text" id="tags"
                           value={this.state.tagsValue}
                           onChange={this.onTagsChange.bind(this)}
                           autoComplete="off" className="form-control"/>
                </div>
            </div>
            <div className="form-group">
                <div className="col-lg-offset-2 col-lg-10">
                    <button className="btn-block btn btn-primary"
                            disabled={this.state.isPushing}
                            onClick={this.push.bind(this)}>{this.state.buttonText}</button>
                </div>
            </div>
        </form>
    }

}
