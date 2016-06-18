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

### Upgrade from v0.4.4 to v0.4.5

Create `settings` table to store crawler offset value.

```SQL
CREATE TABLE `settings` (
  `CrawlerOffset` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```

And add default value.

```SQL
INSERT INTO `settings` (`CrawlerOffset`) VALUES
(0);
```

The previous versions also had an error in the database schema. `Result` in the `apiLog` table should allow nulls.

```SQL
ALTER TABLE `apiLog` CHANGE `Result` `Result` LONGBLOB NULL;
```

### Upgrade from v0.4.5 to v0.5.0

Add a column to the `settings` table for the releases crawler.

```SQL
ALTER TABLE `settings` ADD `ReleaseCrawlerOffset` INT(11) NOT NULL AFTER `CrawlerOffset`;
```

Rename game crawler column to be more explicit.

```SQL
ALTER TABLE `settings` CHANGE `CrawlerOffset` `GameCrawlerOffset` INT(11) NOT NULL;
```