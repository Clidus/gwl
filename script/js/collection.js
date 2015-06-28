var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

var GameCollectionApp = React.createClass({displayName: "GameCollectionApp",
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
            React.createElement("div", null, 
                React.createElement("div", {className: "col-sm-8"}, 
                    React.createElement("div", {className: "row"}
                    )
                ), 
                React.createElement("div", {className: "col-sm-4"}, 
                    React.createElement("div", {className: "row"}, 
                        React.createElement(FilterList, {lists: this.state.lists, onCheckboxChange: this.onCheckboxChange, onAllCheckboxChange: this.onAllCheckboxChange})
                    )
                )
            )
        );
    }
});

var GameList = React.createClass({displayName: "GameList",
    render: function() {
        console.log("GameList");

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