var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

var GameCollectionApp = React.createClass({displayName: "GameCollectionApp",
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
            if(this.props.data.collection) gameList = React.createElement(GameList, {games: this.props.data.collection});
            if(this.props.data.lists) filterList = React.createElement(FilterList, {lists: this.props.data.lists});
        }

        return (
            React.createElement("div", null, 
                React.createElement("div", {className: "col-sm-8"}, 
                    React.createElement("div", {className: "row"}, 
                        gameList 
                    )
                ), 
                React.createElement("div", {className: "col-sm-4"}, 
                    React.createElement("div", {className: "row"}, 
                        filterList 
                    )
                )
            )
        );
    }
});

var GameList = React.createClass({displayName: "GameList",
    render: function() {
            return (
                React.createElement("ul", null, 
                    this.props.games.map(function(game, i) {
                        return (
                            React.createElement("li", {key: game.GBID}, game.Name)
                        );
                    }, this)
                )
            );
    }
});

var FilterList = React.createClass({displayName: "FilterList",
    render: function() {
            return (
                React.createElement("div", null, 
                    React.createElement("b", null, "List"), 
                    React.createElement("ul", {className: "filters"}, 
                        this.props.lists.map(function(list, i) {
                            return (
                                React.createElement("li", {key: list.ListID}, 
                                    React.createElement("label", null, 
                                        React.createElement("input", {id: list.ListID, type: "checkbox"}), " ", list.ListName
                                    )
                                )
                            );
                        }, this)
                    )
                )
            );
    }
});

function loadCollection(){
    React.render(
        React.createElement(GameCollectionApp, null),
        document.getElementById('gameCollection')
    );
}