var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

var GameCollectionApp = React.createClass({displayName: "GameCollectionApp",
    getInitialState: function() {
        return {
            filterLists: []
        };
    },
    // on first load, get list of filters
    componentDidMount: function() {
        this.getFilters();
    },
    // get list of filters
    getFilters: function() {
        console.log("> getFilters");

        $.ajax({
            type : 'POST',
            url : '/user/getCollectionFilters',
            dataType : 'json',
            data: {
                userID: UserID
            },
            success: function(data) {
                // save filters to state
                this.setState({
                    filterLists: data.lists
                });
                // load collection using default filter state
                this.getCollection(data.lists);
            }.bind(this),
            error: function(xhr, status, err) {
                console.error("/user/getCollectionFilters", status, err.toString());
            }.bind(this)
        });
    },
    // get collection using filters
    getCollection: function(filterLists) {
        console.log("> getCollection");

        var lists = { lists: filterLists }

        $.ajax({
            type : 'POST',
            url : '/user/getCollection',
            dataType : 'json',
            data: {
                userID: UserID,
                page: currentPage,
                filters: JSON.stringify(lists)
            },
            success: function(data) {
                // save collection to be passed to GameList
                this.setProps({
                    games: data.collection
                });
            }.bind(this),
            error: function(xhr, status, err) {
                console.error("/user/getCollectionFilters", status, err.toString());
            }.bind(this)
        });
    },
    // on filter change
    onCheckboxChange: function(ListID) {
        // update Selected state of changed filter
        var lists = this.state.filterLists.map(function(d) {
            return {
                ListID: d.ListID,
                ListName: d.ListName,
                Selected: (d.ListID === ListID ? !d.Selected : d.Selected)
            };
        });

        // update state of filters
        this.setState({ filterLists: lists });

        // reload collection based on new filters
        this.getCollection(lists);
    },
    // on All / None checkbox change
    onAllCheckboxChange: function(CheckedValue) {
        // change all filters to checked or unchecked
        var lists = this.state.filterLists.map(function(d) {
            return { ListID: d.ListID, ListName: d.ListName, Selected: CheckedValue };
        });

        // update state of filters
        this.setState({ filterLists: lists });

        // reload collection based on new filters
        this.getCollection(lists);
    },
    render: function() {
        console.log("GameCollectionApp");

        return (
            React.createElement("div", null, 
                React.createElement("div", {className: "col-sm-8"}, 
                    React.createElement("div", {className: "row"}, 
                        React.createElement(GameList, {games: this.props.games})
                    )
                ), 
                React.createElement("div", {className: "col-sm-4"}, 
                    React.createElement("div", {className: "row"}, 
                        React.createElement(FilterList, {lists: this.state.filterLists, onCheckboxChange: this.onCheckboxChange, onAllCheckboxChange: this.onAllCheckboxChange})
                    )
                )
            )
        );
    }
});

// list of games
var GameList = React.createClass({displayName: "GameList",
    render: function() {
        console.log("GameList");

        if(this.props.games == null)
            return null;

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

// list of filters
var FilterList = React.createClass({displayName: "FilterList",
    getInitialState: function() {
        return {
            checkAll: true
        };
    },
    onCheckboxChange: function(ListID) {
        // call parents onCheckboxChange 
        this.props.onCheckboxChange(ListID);
    },
    onAllCheckboxChange: function() {
        // update state of checkAll
        this.setState({
            checkAll: !this.state.checkAll
        });

        // call parents onAllCheckboxChange
        this.props.onAllCheckboxChange(!this.state.checkAll);
    },
    render: function() {
        console.log("FilterList");

        // if no lists passed, display nothing
        if(this.props.lists == null)
            return null;

        var checks = this.props.lists.map(function(list) {
            return (
               React.createElement("li", {key: list.ListID}, 
                    React.createElement("input", {id: list.ListID, type: "checkbox", checked: list.Selected, onChange: this.onCheckboxChange.bind(this, list.ListID)}), " ", list.ListName
                )
            );
        }.bind(this));
        return (
            React.createElement("div", null, 
                React.createElement("b", null, "List"), 
                React.createElement("ul", null, 
                    React.createElement("li", null, 
                        React.createElement("input", {type: "checkbox", ref: "globalSelector", onChange: this.onAllCheckboxChange, checked: this.state.checkAll}), " All / None"
                    ), 
                    checks
                )
            )
        );
    }
})

function loadCollection(){
    React.render(
        React.createElement(GameCollectionApp, null),
        document.getElementById('gameCollection')
    );
}