var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

var GameCollectionApp = React.createClass({
    getInitialState: function() {
        return {
            filterLists: [],
            filterStatuses: []
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
                    filterLists: data.lists,
                    filterStatuses: data.statuses
                });
                // load collection using default filter state
                this.getCollection(data.lists, data.statuses);
            }.bind(this),
            error: function(xhr, status, err) {
                console.error("/user/getCollectionFilters", status, err.toString());
            }.bind(this)
        });
    },
    // get collection using filters
    getCollection: function(filterLists, filterStatuses) {
        console.log("> getCollection");

        var lists = { lists: filterLists, statuses: filterStatuses }

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
    onCheckboxChange: function(filterType, id) {
        // update Selected state of changed filter
        var lists = filterType == "List" ? this.changeFilterStatus(this.state.filterLists, id) : this.state.filterLists;
        var statuses = filterType == "Completion" ? this.changeFilterStatus(this.state.filterStatuses, id) : this.state.filterStatuses;

        // update state of filters
        this.setState({ filterLists: lists, filterStatuses: statuses });

        // reload collection based on new filters
        this.getCollection(lists, statuses);
    },
    // on All / None checkbox change
    onAllCheckboxChange: function(filterType, checkedValue) {
        // change all filters to checked or unchecked
        var lists = filterType == "List" ? this.changeAllFiltersStatus(this.state.filterLists, checkedValue) : this.state.filterLists;
        var statuses = filterType == "Completion" ? this.changeAllFiltersStatus(this.state.filterStatuses, checkedValue) : this.state.filterStatuses;

        // update state of filters
        this.setState({ filterLists: lists, filterStatuses: statuses });

        // reload collection based on new filters
        this.getCollection(lists, statuses);
    },
    changeFilterStatus: function(filter, id) {
        return filter.map(function(d) {
            return {
                ID: d.ID,
                Name: d.Name,
                Games: d.Games,
                Selected: (d.ID === id ? !d.Selected : d.Selected)
            };
        });
    },
    changeAllFiltersStatus: function(filter, checkedValue) {
        return filter.map(function(d) {
            return { ID: d.ID, Name: d.Name, Games: d.Games, Selected: checkedValue };
        });
    },
    render: function() {
        console.log("GameCollectionApp");

        return (
            <div>
                <div className="col-sm-8">
                    <div className="row">
                        <Games games={this.props.games} />
                    </div>
                </div>
                <div className="col-sm-4">
                    <div className="row">
                        <Filters filterType="List" lists={this.state.filterLists} onCheckboxChange={this.onCheckboxChange} onAllCheckboxChange={this.onAllCheckboxChange} />
                        <Filters filterType="Completion" lists={this.state.filterStatuses} onCheckboxChange={this.onCheckboxChange} onAllCheckboxChange={this.onAllCheckboxChange} />
                    </div>
                </div>
            </div>
        );
    }
});

// list of games
var Games = React.createClass({
    render: function() {
        console.log("GameList");

        if(this.props.games == null)
            return null;

        return (
            <ul>
                {this.props.games.map(function(game, i) {
                    var link = "/game/" + game.GBID;
                    return (
                        <li key={game.GBID}><a href={link}>{game.Name}</a></li>
                    );
                }, this)}
            </ul>
        );
    }
});

// list of filters
var Filters = React.createClass({
    getInitialState: function() {
        return {
            checkAll: true
        };
    },
    onCheckboxChange: function(ID) {
        // call parents onCheckboxChange 
        this.props.onCheckboxChange(this.props.filterType, ID);
    },
    onAllCheckboxChange: function() {
        // update state of checkAll
        this.setState({
            checkAll: !this.state.checkAll
        });

        // call parents onAllCheckboxChange
        this.props.onAllCheckboxChange(this.props.filterType, !this.state.checkAll);
    },
    render: function() {
        console.log("Filter: " + this.props.filterType);

        // if no lists passed, display nothing
        if(this.props.lists == null)
            return null;

        var checks = this.props.lists.map(function(list) {
            return (
               <li key={list.ID}>
                    <input id={list.ID} type="checkbox" checked={list.Selected} onChange={this.onCheckboxChange.bind(this, list.ID)} /> {list.Name} ({list.Games})
                </li>
            );
        }.bind(this));
        return (
            <div>
                <b>{this.props.filterType}</b>
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