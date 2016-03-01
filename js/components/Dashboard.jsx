/**
 * @author Victor Häggqvist
 * @since 3/1/16
 */

var React = require('react');
import {LinkList} from './LinkList.jsx';
import {PushForm} from './PushForm.jsx';
import {log} from '../log';

export class Dashboard extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            list: []
        };

        this.loadRecets();
    }

    loadRecets() {
        fetch('/api/items', {
            credentials: 'include'
        }).then(resp => resp.json())
            .then(items => {
                this.setState({list: items});
            })
    }

    popLink(id) {
        this.setState({list: this.state.list.filter(i => i.id != id)});
    }

    render() {
        return <div>
            <div className="container">
                <div className="row">
                    <div className="col-md-6">
                        <h3>Push new</h3>
                        <PushForm onPushed={() => this.loadRecets()} />
                    </div>
                </div>
            </div>
            <div className="container-fluid">
                <div className="row">
                    <div className="col-md-12">
                        <h3>Recently pushed</h3>
                        <LinkList list={this.state.list} onPopedLink={this.popLink.bind(this)} />
                    </div>
                </div>
            </div>
        </div>
    }

}
