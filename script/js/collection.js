var GameCollection = React.createClass({displayName: "GameCollection",
    render: function() {
        console.log("React!");
        return (
            React.createElement("div", null, 
                React.createElement("h1", null, "Video Games!")
            )
        );
    }
});

function loadCollection(){
    React.render(
        React.createElement(GameCollection, null),
        document.getElementById('gameCollection')
    );
}