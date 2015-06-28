var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

var GameCollectionApp = React.createClass({
    getInitialState: function() {
        return {
            lists: []
        };
    },
    componentDidMount: function() {
        this.getFilters();
    },
    onCheckboxChange: function(ListID) {
         var lists = this.state.lists.map(function(d) {
            return {
                ListID: d.ListID,
                ListName: d.ListName,
                Selected: (d.ListID === ListID ? !d.Selected : d.Selected)
            };
        });

        this.setState({ lists: lists });
    },
    onAllCheckboxChange: function(CheckedValue) {
        var lists = this.state.lists.map(function(d) {
            return { ListID: d.ListID, ListName: d.ListName, Selected: CheckedValue };
        });

        this.setState({ lists: lists });
    },
    getFilters: function() {
        $.ajax({
            type : 'POST',
            url : '/user/getCollectionFilters',
            dataType : 'json',
            data: {
                userID: UserID//,
                //page: currentPage,
                //filters: JSON.stringify(filters)
            },
            success: function(data) {
                this.setState({
                    lists: data.lists
                });
            }.bind(this),
            error: function(xhr, status, err) {
                console.error("/user/getCollectionFilters", status, err.toString());
            }.bind(this)
        });
    },
    render: function() {
        console.log("GameCollectionApp");

        return (
            <div>
                <div className="col-sm-8">
                    <div className="row">
                    </div>
                </div>
                <div className="col-sm-4">
                    <div className="row">
                        <FilterList lists={this.state.lists} onCheckboxChange={this.onCheckboxChange} onAllCheckboxChange={this.onAllCheckboxChange} />
                    </div>
                </div>
            </div>
        );
    }
});

var GameList = React.createClass({
    render: function() {
        console.log("GameList");

        return (
            <ul>
                {this.props.games.map(function(game, i) {
                    return (
                        <li key={game.GBID}>{game.Name}</li>
                    );
                }, this)}
            </ul>
        );
    }
});

var FilterList = React.createClass({
    getInitialState: function() {
        return {
            checkAll: true
        };
    },
    onCheckboxChange: function(ListID) {
        this.props.onCheckboxChange(ListID);
    },
    onAllCheckboxChange: function() {
        this.setState({
            checkAll: !this.state.checkAll
        });
        this.props.onAllCheckboxChange(!this.state.checkAll);
    },
    render: function() {
        console.log("FilterList");

        if(this.props.lists == null)
            return null;

        var checks = this.props.lists.map(function(list) {
            return (
               <li key={list.ListID}>
                    <input id={list.ListID} type="checkbox" checked={list.Selected} onChange={this.onCheckboxChange.bind(this, list.ListID)} /> {list.ListName}
                </li>
            );
        }.bind(this));
        return (
            <div>
                <b>List</b>
                <ul>
                    <li>
                        <input type="checkbox" ref="globalSelector" onChange={this.onAllCheckboxChange} checked={this.state.checkAll} /> All / None
                    </li>
                    {checks}
                </ul>
            </div>
        );
    }
})

function loadCollection(){
    React.render(
        <GameCollectionApp />,
        document.getElementById('gameCollection')
    );
}