/**
 * @author Victor Häggqvist
 * @since 3/1/16
 */

var React = require('react');
import {log} from '../log';

export class LinkList extends React.Component {

    render() {
        return <table className="table table-striped table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>URL</th>
                <th>Title</th>
                <th>Tags</th>
                <th>Timestamp</th>
                <th/>
            </tr>
            </thead>
            <tbody>
                {this.props.list.map(l => <Link key={l.id} {...l} onPopedLink={this.props.onPopedLink.bind(this)} />)}
            </tbody>
        </table>
    }

}

const truncate = (text, length) => {
    text = text.trim();
    if (text.length < length) return text;

    return text.substring(0, length) + "...";
};

class Link extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            isDeleting: false
        }
    }

    deleteLink() {
        this.setState({isDeleting: true});

        fetch('/api/items/' + this.props.id, {
            method: 'delete',
            credentials: 'include'
        }).then(resp => {
            if (resp.status === 204) {
                this.setState({isDeleting: false});
                this.props.onPopedLink(this.props.id);
            }

            log.info(resp);
        }, err => log.error(err));
    }

    render() {
        const editUrl= '/stack/' + this.props.id;
        const favicon = <img src={'https://www.google.com/s2/favicons?domain='+this.props.url} alt="favicon"/>;

        let buttonClasses = 'btn btn-default btn-sm delete-btn icon-button';
        if (this.state.isDeleting) buttonClasses += 'spinner';

        return <tr>
            <td><a href={editUrl}>{this.props.id}</a></td>
            <td>{favicon} <a href={this.props.url}>{truncate(this.props.url, 70)}</a></td>
            <td title={this.props.title}>{truncate(this.props.title, 70)}</td>
            <td title={this.props.tags.reduce((a, b) => a+', '+b)}>{truncate(this.props.tags.reduce((a, b) => a+', '+b), 40)}</td>
            <td className="date">{this.props.created.substring(0, 16)}</td>
            <td className="action">
                <button type="button" className={buttonClasses} onClick={this.deleteLink.bind(this)}>
                    <span className="glyphicon glyphicon-trash" aria-hidden="true"/>
                </button>
            </td>
        </tr>;
    }

}
