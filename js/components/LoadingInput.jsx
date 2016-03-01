/**
 * @author Victor Häggqvist
 * @since 3/1/16
 */

var React = require('react');

export class LoadingInput extends React.Component {

    render() {
        if (this.props.loading) {
            return <div className="form-group has-feedback">
                <label className="control-label col-lg-2 required" htmlFor={this.props.id}>{this.props.label}</label>
                <div className="col-lg-10">
                    <input type="text" id="title" required="required" autoComplete="off"
                           value={this.props.value}
                           className="form-control" onChange={this.props.onChange.bind(this)}/>
                    <span className="glyphicon glyphicon-repeat form-control-feedback"
                          style={{animation: 'spin 2s infinite linear'}} aria-hidden="true"/>
                    <span id="inputSuccess2Status" className="sr-only">(loading)</span>
                </div>
            </div>;
        } else {
            return <div className="form-group has-feedback">
                <label className="control-label col-lg-2 required" htmlFor={this.props.id}>{this.props.label}</label>
                <div className="col-lg-10">
                    <input type="text" id={this.props.id} required="required" autoComplete="off"
                           value={this.props.value}
                           className="form-control" onChange={this.props.onChange.bind(this)} />
                </div>
            </div>;
        }
    }

}
