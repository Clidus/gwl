var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

var GameCollectionApp = React.createClass({
    getInitialState: function() {
        return {
            filterLists: [],
            filterStatuses: [],
            filterPlatforms: [],
            sort: [
                    { "ID": 0, "Name":"Release Date (Newest)", "Selected": true },
                    { "ID": 1, "Name":"Release Date (Oldest)", "Selected": false },
                    { "ID": 2, "Name":"Name (A-Z)", "Selected": false },
                    { "ID": 3, "Name":"Name (Z-A)", "Selected": false },
                    { "ID": 4, "Name":"Hours Played (Most)", "Selected": false },
                    { "ID": 5, "Name":"Hours Played (Least)", "Selected": false }
                ]
        };
    },
    // on first load, get list of filters
    componentDidMount: function() {
        this.getFilters();
    },
    // get list of filters
    getFilters: function() {
        //console.log("> getFilters");

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
                    filterStatuses: data.statuses,
                    filterPlatforms: data.platforms
                });
                // load collection using default filter state
                this.getCollection(data.lists, data.statuses, data.platforms, this.state.sort);
            }.bind(this),
            error: function(xhr, status, err) {
                console.error("/user/getCollectionFilters", status, err.toString());
            }.bind(this)
        });
    },
    // get collection using filters
    getCollection: function(filterLists, filterStatuses, filterPlatforms, sort) {
        //console.log("> getCollection");

        var lists = { lists: filterLists, statuses: filterStatuses, platforms: filterPlatforms, orderBy: sort }

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
        var platforms = filterType == "Platform" ? this.changeFilterStatus(this.state.filterPlatforms, id) : this.state.filterPlatforms;

        // update state of filters
        this.setState({ filterLists: lists, filterStatuses: statuses, filterPlatforms: platforms });

        // reload collection based on new filters
        this.getCollection(lists, statuses, platforms, this.state.sort);
    },
    // on All / None checkbox change
    onAllCheckboxChange: function(filterType, checkedValue) {
        // change all filters to checked or unchecked
        var lists = filterType == "List" ? this.changeAllFiltersStatus(this.state.filterLists, checkedValue) : this.state.filterLists;
        var statuses = filterType == "Completion" ? this.changeAllFiltersStatus(this.state.filterStatuses, checkedValue) : this.state.filterStatuses;
        var platforms = filterType == "Platform" ? this.changeAllFiltersStatus(this.state.filterPlatforms, checkedValue) : this.state.filterPlatforms;

        // update state of filters
        this.setState({ filterLists: lists, filterStatuses: statuses, filterPlatforms: platforms });

        // reload collection based on new filters
        this.getCollection(lists, statuses, platforms, this.state.sort);
    },
    onRadioChange: function(id) {
        // enable selected sort and disable the rest
        var sort = this.state.sort.map(function(d) {
            return {
                ID: d.ID,
                Name: d.Name,
                Selected: (d.ID === id ? true : false)
            };
        });

        // update state of sort
        this.setState({ sort: sort });        

        // reload collection with new sort
        this.getCollection(this.state.filterLists, this.state.filterStatuses, this.state.filterPlatforms, sort);
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
        //console.log("GameCollectionApp");

        return (
            <div>
                <div className="col-sm-8">
                    <div className="row">
                        <Games games={this.props.games} />
                    </div>
                </div>
                <div className="col-sm-4">
                    <div className="row">
                        <Sorts sort={this.state.sort} onRadioChange={this.onRadioChange} />
                        <Filters filterType="List" lists={this.state.filterLists} onCheckboxChange={this.onCheckboxChange} onAllCheckboxChange={this.onAllCheckboxChange} />
                        <Filters filterType="Completion" lists={this.state.filterStatuses} onCheckboxChange={this.onCheckboxChange} onAllCheckboxChange={this.onAllCheckboxChange} />
                        <Filters filterType="Platform" lists={this.state.filterPlatforms} onCheckboxChange={this.onCheckboxChange} onAllCheckboxChange={this.onAllCheckboxChange} />
                    </div>
                </div>
            </div>
        );
    }
});

// list of games
var Games = React.createClass({
    render: function() {
        //console.log("GameList");

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
        //console.log("Filter: " + this.props.filterType);

        // if no lists passed, display nothing
        if(this.props.lists == null)
            return null;

        var filters = this.props.lists.map(function(list) {
            return (
               <li key={list.ID}>
                    <input id={list.ID} type="checkbox" checked={list.Selected} onChange={this.onCheckboxChange.bind(this, list.ID)} /> {list.Name} ({list.Games})
                </li>
            );
        }.bind(this));
        return (
            <div>
                <b>{this.props.filterType}</b>
                <ul className="filters">
                    <li>
                        <input type="checkbox" ref="globalSelector" onChange={this.onAllCheckboxChange} checked={this.state.checkAll} /> All / None
                    </li>
                    {filters}
                </ul>
            </div>
        );
    }
})

// list of filters
var Sorts = React.createClass({
    onRadioChange: function(ID) {
        // call parents onRadioChange 
        this.props.onRadioChange(ID);
    },
    render: function() {
        // if no sort passed, display nothing
        if(this.props.sort == null)
            return null;

        var sorts = this.props.sort.map(function(sort) {
            return (
               <li key={sort.ID}>
                    <input name="orderBy" type="radio" value={sort.Selected} checked={sort.Selected} onChange={this.onRadioChange.bind(this, sort.ID)} /> {sort.Name}
                </li>
            );
        }.bind(this));
        return (
            <div>
                <b>Order By</b>
                <ul className="filters">
                    {sorts}
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