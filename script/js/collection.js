var GameCollection = React.createClass({
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
        document.getElementById("gameCollection")
    );
}