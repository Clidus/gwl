var GameCollection = React.createClass({
    render: function() {
        console.log("React!");
        return (
            <div>
                <h1>Video Games!</h1>
            </div>
        );
    }
});

function loadCollection(){
    React.render(
        <GameCollection />,
        document.getElementById('gameCollection')
    );
}