var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

var GameCollectionApp = React.createClass({
    getInitialState: function(){
        return { collection: [] };
    },
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
                this.setProps({ collection: data.collection });
                console.log(data.collection);
            }.bind(this),
            error: function(xhr, status, err) {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },
    render: function() {
        return (
            <GameList games={this.props.collection} />
        );
    }
});

var GameList = React.createClass({
    render: function() {
        if (this.props.games) {
            return (
                <ul>
                    {this.props.games.map(function(game, i) {
                      return (
                        <li key={game.GBID}>{game.Name}</li>
                      );
                    }, this)}
                </ul>
            );
        } else {
            return null;
        }
    }
});

function loadCollection(){
    React.render(
        <GameCollectionApp />,
        document.getElementById('gameCollection')
    );
}