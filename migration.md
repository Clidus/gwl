## Database migration between versions

Follow the instructions bellow to migrate your GWL database between versions. All instructions should be followed if you are upgrading multiple versions.

### Upgrade from v0.4.3 to v0.4.4

Add `GBLink` to `Games` table.

```SQL
ALTER TABLE `games` ADD `GBLink` VARCHAR(300) DEFAULT NULL AFTER `Error`;
```

Previous versions logged the json response from Giant Bomb's API into the `apiLog` table. This version only logs the json response for searches. 

Run the following query to delete the game json data that is not required.

```SQL
UPDATE apiLog SET Result = NULL WHERE RequestType = "Game"
```