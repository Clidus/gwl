var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

var GameCollectionApp = React.createClass({
    componentDidMount: function() {
        this.getCollection();
    },
    getCollection: function() {
        $.ajax({
            type : 'POST',
            url : '/user/getCollection',
            dataType : 'json',
            data: {
                userID: UserID,
                page: currentPage,
                filters: JSON.stringify(filters)
            },
            success: function(data) {
                this.setProps({ data: data });
                console.log(data);
            }.bind(this),
            error: function(xhr, status, err) {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },
    render: function() {
        var gameList;
        var filterList;
        if (this.props.data) {
            if(this.props.data.collection) gameList = <GameList games={this.props.data.collection} />;
            if(this.props.data.lists) filterList = <FilterList lists={this.props.data.lists} />;
        }

        return (
            <div>
                <div className="col-sm-8">
                    <div className="row">
                        { gameList }
                    </div>
                </div>
                <div className="col-sm-4">
                    <div className="row">
                        { filterList }
                    </div>
                </div>
            </div>
        );
    }
});

var GameList = React.createClass({
    render: function() {
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
    render: function() {
            return (
                <div>
                    <b>List</b>
                    <ul className="filters">
                        {this.props.lists.map(function(list, i) {
                            return (
                                <li key={list.ListID}>
                                    <label>
                                        <input id={list.ListID} type="checkbox" /> {list.ListName}
                                    </label>
                                </li>
                            );
                        }, this)}
                    </ul>
                </div>
            );
    }
});

function loadCollection(){
    React.render(
        <GameCollectionApp />,
        document.getElementById('gameCollection')
    );
}