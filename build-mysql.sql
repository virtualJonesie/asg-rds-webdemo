-- Select the WebDemo Database
USE webdemo;

-- Create the Regions Table
CREATE TABLE regions (code VARCHAR(32), name VARCHAR(64), zones INT);

-- Load data from a local file into the Regions Table;
LOAD DATA LOCAL INFILE '/home/ec2-user/regions.txt' INTO TABLE regions;

-- Verify data was loaded
SELECT * FROM regions;