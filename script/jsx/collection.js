var GameCollectionApp = React.createClass({
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
                // starting state
                var page = 1;
                var sort = this.state.sort;
                var lists = data.lists;

                // get page state from url
                var urlHash = $.address.pathNames();

                // loop through each item in url hash
                urlHash.forEach(function(pageState) {
                    var itemState = pageState.split('=');

                    switch(itemState[0]) {
                        case "page":
                            page = parseInt(itemState[1]);
                            break;
                        case "sort":
                            sort = this.changeSort(parseInt(itemState[1]));
                            break;
                        case "lists":
                            lists = this.setupFilterStatus(lists, itemState[1].split(','));
                            break;
                    }
                }.bind(this));

                // save filters to state
                this.setState({
                    filterLists: lists,
                    filterStatuses: data.statuses,
                    filterPlatforms: data.platforms,
                    page: page,
                    sort: sort
                });

                // load collection using default filter state
                this.getCollection(lists, data.statuses, data.platforms, sort, page);
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
                // update url hash
                this.updateUrlHash(filterLists, filterStatuses, filterPlatforms, sort, page);

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
    // update page state in url hash
    updateUrlHash: function(filterLists, filterStatuses, filterPlatforms, sort, page) {
        // find the current sort
        var selectedSort = 0;
        for(i = 0; i < sort.length; i++) {
            if(sort[i].Selected) {
                selectedSort = sort[i].ID;
                break;
            }
        };

        var selectedLists = "";
        for(i = 0; i < filterLists.length; i++) {
            if(filterLists[i].Selected) {
                selectedLists += filterLists[i].ID + ",";
            }
        };

        // update hash
        var hash = "page=" + page + "/sort=" + selectedSort + "/lists=" + selectedLists;
        $.address.value(hash);
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
    changeSort: function(id) {
        // enable selected sort and disable the rest
        return this.state.sort.map(function(d) {
            return {
                ID: d.ID,
                Name: d.Name,
                Selected: (d.ID === id ? true : false)
            };
        });
    },
    onRadioChange: function(id) {
        // enable selected sort and disable the rest
        var sort = this.changeSort(id);
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
    setupFilterStatus: function(filters, ids) {
        // for each filter
        filters.forEach(function(filter) {
            // default to unselected
            filter.Selected = false;
            // check if its in list of selected ids
            for(i = 0; i < ids.length; i++) {
                // if filter is found, mark as selected
                if(filter.ID == ids[i])
                {
                    filter.Selected = true;
                    break;
                }
            }
        });

        return filters;
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
            <div>
                <div className="col-sm-8">
                    <div className="row">
                        <Games games={this.props.games} />
                        <Navigation page={this.state.page} onPageChange={this.onPageChange} stats={this.props.stats} />
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

// next / previous buttons
var Navigation = React.createClass({    
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
            previousPage = <li className="previous handPointer"><a onClick={this.onPageChange.bind(this, this.props.page - 1)}><span aria-hidden="true">&larr;</span> Previous</a></li>;

        // next page button
        var nextPage;
        if(this.props.page < lastPage)
            nextPage = <li className="next handPointer"><a onClick={this.onPageChange.bind(this, this.props.page + 1)}><span aria-hidden="true">&rarr;</span> Next</a></li>

        return (
            <nav>
                <ul className="pager">
                    {previousPage}
                    {nextPage}
                </ul>
            </nav>
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