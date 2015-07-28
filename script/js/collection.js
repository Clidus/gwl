var GameCollectionApp = React.createClass({displayName: "GameCollectionApp",
    getInitialState: function() {
        return {
            page: 1,
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
                this.getCollection(data.lists, data.statuses, data.platforms, this.state.sort, this.state.page);
            }.bind(this),
            error: function(xhr, status, err) {
                console.error("/user/getCollectionFilters", status, err.toString());
            }.bind(this)
        });
    },
    // get collection using filters
    getCollection: function(filterLists, filterStatuses, filterPlatforms, sort, page) {
        var lists = { lists: filterLists, statuses: filterStatuses, platforms: filterPlatforms, orderBy: sort }

        $.ajax({
            type : 'POST',
            url : '/user/getCollection',
            dataType : 'json',
            data: {
                userID: UserID,
                page: page,
                filters: JSON.stringify(lists)
            },
            success: function(data) {
                // save collection to be passed to GameList
                this.setProps({
                    games: data.collection,
                    stats: data.stats
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
        var page = 1;

        // update state of filters
        this.setState({ filterLists: lists, filterStatuses: statuses, filterPlatforms: platforms, page: page });

        // reload collection based on new filters
        this.getCollection(lists, statuses, platforms, this.state.sort, page);
    },
    // on All / None checkbox change
    onAllCheckboxChange: function(filterType, checkedValue) {
        // change all filters to checked or unchecked
        var lists = filterType == "List" ? this.changeAllFiltersStatus(this.state.filterLists, checkedValue) : this.state.filterLists;
        var statuses = filterType == "Completion" ? this.changeAllFiltersStatus(this.state.filterStatuses, checkedValue) : this.state.filterStatuses;
        var platforms = filterType == "Platform" ? this.changeAllFiltersStatus(this.state.filterPlatforms, checkedValue) : this.state.filterPlatforms;
        var page = 1;

        // update state of filters
        this.setState({ filterLists: lists, filterStatuses: statuses, filterPlatforms: platforms, page: page });

        // reload collection based on new filters
        this.getCollection(lists, statuses, platforms, this.state.sort, page);
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
        var page = 1;

        // update state of sort
        this.setState({ sort: sort, page: page });        

        // reload collection with new sort
        this.getCollection(this.state.filterLists, this.state.filterStatuses, this.state.filterPlatforms, sort, page);
    },
    onPageChange: function(page) {
        // update page in state
        this.setState({ page: page });        

        // reload collection with new page
        this.getCollection(this.state.filterLists, this.state.filterStatuses, this.state.filterPlatforms, this.state.sort, page);
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
        return (
            React.createElement("div", null, 
                React.createElement("div", {className: "col-sm-8"}, 
                    React.createElement("div", {className: "row"}, 
                        React.createElement(Games, {games: this.props.games}), 
                        React.createElement(Navigation, {page: this.state.page, onPageChange: this.onPageChange, stats: this.props.stats})
                    )
                ), 
                React.createElement("div", {className: "col-sm-4"}, 
                    React.createElement("div", {className: "row"}, 
                        React.createElement(Sorts, {sort: this.state.sort, onRadioChange: this.onRadioChange}), 
                        React.createElement(Filters, {filterType: "List", lists: this.state.filterLists, onCheckboxChange: this.onCheckboxChange, onAllCheckboxChange: this.onAllCheckboxChange}), 
                        React.createElement(Filters, {filterType: "Completion", lists: this.state.filterStatuses, onCheckboxChange: this.onCheckboxChange, onAllCheckboxChange: this.onAllCheckboxChange}), 
                        React.createElement(Filters, {filterType: "Platform", lists: this.state.filterPlatforms, onCheckboxChange: this.onCheckboxChange, onAllCheckboxChange: this.onAllCheckboxChange})
                    )
                )
            )
        );
    }
});

// list of games
var Games = React.createClass({displayName: "Games",
    render: function() {
        if(this.props.games == null)
            return null;

        return (
            React.createElement("ul", null, 
                this.props.games.map(function(game, i) {
                    var link = "/game/" + game.GBID;
                    return (
                        React.createElement("li", {key: game.GBID}, React.createElement("a", {href: link}, game.Name))
                    );
                }, this)
            )
        );
    }
});

// next / previous buttons
var Navigation = React.createClass({displayName: "Navigation",    
    onPageChange: function(page) {
        // call parents onCheckboxChange 
        this.props.onPageChange(page);
    },
    render: function() {
        if(this.props.page == null || this.props.stats == null)
            return null;

        // calculate how many pages there are
        var resultsPerPage = 10;
        var firstPage = 1;
        var lastPage = Math.ceil(this.props.stats.Total / resultsPerPage);
        
        // previous page button
        var previousPage;
        if(this.props.page > firstPage)
            previousPage = React.createElement("li", {className: "previous handPointer"}, React.createElement("a", {onClick: this.onPageChange.bind(this, this.props.page - 1)}, React.createElement("span", {"aria-hidden": "true"}, "←"), " Previous"));

        // next page button
        var nextPage;
        if(this.props.page < lastPage)
            nextPage = React.createElement("li", {className: "next handPointer"}, React.createElement("a", {onClick: this.onPageChange.bind(this, this.props.page + 1)}, React.createElement("span", {"aria-hidden": "true"}, "→"), " Next"))

        return (
            React.createElement("nav", null, 
                React.createElement("ul", {className: "pager"}, 
                    previousPage, 
                    nextPage
                )
            )
        );
    }
});

// list of filters
var Filters = React.createClass({displayName: "Filters",
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
        // if no lists passed, display nothing
        if(this.props.lists == null)
            return null;

        var filters = this.props.lists.map(function(list) {
            return (
               React.createElement("li", {key: list.ID}, 
                    React.createElement("input", {id: list.ID, type: "checkbox", checked: list.Selected, onChange: this.onCheckboxChange.bind(this, list.ID)}), " ", list.Name, " (", list.Games, ")"
                )
            );
        }.bind(this));
        return (
            React.createElement("div", null, 
                React.createElement("b", null, this.props.filterType), 
                React.createElement("ul", {className: "filters"}, 
                    React.createElement("li", null, 
                        React.createElement("input", {type: "checkbox", ref: "globalSelector", onChange: this.onAllCheckboxChange, checked: this.state.checkAll}), " All / None"
                    ), 
                    filters
                )
            )
        );
    }
})

// list of filters
var Sorts = React.createClass({displayName: "Sorts",
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
               React.createElement("li", {key: sort.ID}, 
                    React.createElement("input", {name: "orderBy", type: "radio", value: sort.Selected, checked: sort.Selected, onChange: this.onRadioChange.bind(this, sort.ID)}), " ", sort.Name
                )
            );
        }.bind(this));
        return (
            React.createElement("div", null, 
                React.createElement("b", null, "Order By"), 
                React.createElement("ul", {className: "filters"}, 
                    sorts
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