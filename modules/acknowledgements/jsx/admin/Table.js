class Table extends React.Component {
    constructor (props) {
        super(props);
        this.state = {};
        this.tbody = <TBody
            id_prefix={props["id_prefix"]}
            center_id={props["center_id"]}
            fetch_all_url={props["fetch_all_url"]}
            delete_url={props["delete_url"]}
            update_url={props["update_url"]}
            />
    }
    onAddSubmit (e) {
        e.preventDefault();
        
        const val = $("#"+this.props.id_prefix+"-add-input").val();
        console.log(val);
        $.ajax({
            type: "POST",
            url : this.props["insert_url"],
            dataType: "json",
            data: {
                "center_id":this.props["center_id"],
                "title":val
            },
            success: function (data) {
                console.log(data);
                $.ajax({
                    method: "GET",
                    url : this.props["fetch_url"] + "?id=" + encodeURIComponent(data.id),
                    dataType: "json",
                    success: function (d) {
                        window.dispatchEvent(new CustomEvent(this.props.id_prefix+"-insert", {
                            detail:d
                        }));
                        this.setState({
                            error:null
                        });
                    }.bind(this),
                    error: function (error) {
                        console.log(error);
                        this.setState({
                            error:error.responseJSON.error
                        });
                    }.bind(this)
                });
            }.bind(this),
            error: function (error) {
                console.log(error);
                this.setState({
                    error:error.responseJSON.error
                });
            }.bind(this)
        });
    }
    render () {
        return (
            <div>
                <FormAdd
                    id_prefix={this.props.id_prefix}
                    center_id={this.props.center_id}
                    insert_url={this.props.insert_url}
                    fetch_url={this.props.fetch_url}
                    placeholder={this.props.placeholder}
                    />
                <table className="table table-hover table-primary table-bordered table-unresolved-conflicts dynamictable">
                    <thead>
                        <tr className="info">
                            <th>{this.props.placeholder}</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    {this.tbody}
                </table>
            </div>
        );
    }
}

window.Table = Table;