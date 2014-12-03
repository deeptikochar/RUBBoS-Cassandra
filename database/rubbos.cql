CREATE KEYSPACE IF NOT EXISTS rubbos WITH REPLICATION = {'class' : 'SimpleStrategy', 'replication_factor' : 1};
USE rubbos;

CREATE TABLE categories (
	id INT,
	name VARCHAR,
	PRIMARY KEY(id, name)
) WITH CLUSTERING ORDER BY (name ASC) AND COMPACT STORAGE;

CREATE TABLE users (
	id TIMEUUID,
	firstname VARCHAR,
	lastname VARCHAR,
	nickname VARCHAR,
	password VARCHAR,
	email VARCHAR,
	rating INT,
	access INT,
	creation_date timestamp,
	PRIMARY KEY(id)
) WITH COMPACT STORAGE;

CREATE TABLE user_logins (
	nickname VARCHAR,
	password VARCHAR,
	id TIMEUUID,
	access INT,
	PRIMARY KEY(nickname, password)
);

CREATE TABLE stories (
	id TIMEUUID,
	title VARCHAR,
	body TEXT,
	date TIMESTAMP,
	writer TIMEUUID,
	category INT,
	PRIMARY KEY(id, date)
)
WITH CLUSTERING ORDER BY (date DESC);

CREATE INDEX stories_category
	on stories(category);

CREATE TABLE old_stories (
        id TIMEUUID,
        title VARCHAR,
        body TEXT,
	date TIMESTAMP,
        writer TIMEUUID,
        category INT,
        PRIMARY KEY(id, date)
)
WITH CLUSTERING ORDER BY (date DESC);

CREATE INDEX old_stories_category
        on old_stories(category);

CREATE TABLE submissions (
	id TIMEUUID,
	title VARCHAR,
        body TEXT,
        date TIMESTAMP,
        writer TIMEUUID,
        category INT,
        PRIMARY KEY(id, date)
) 
WITH CLUSTERING ORDER BY (date DESC);

CREATE TABLE comments (
	id TIMEUUID,
	writer TIMEUUID,
	story_id TIMEUUID,
	parent TIMEUUID,
	childs BIGINT,
	rating INT,
	date TIMESTAMP,
	subject VARCHAR,
	comment TEXT,
	PRIMARY KEY(id, date)
)
WITH CLUSTERING ORDER BY (date DESC);

CREATE INDEX comments_rating
	ON comments(rating);

CREATE INDEX comments_parent
	ON comments(parent);

CREATE INDEX comments_story_id
	ON comments(story_id);

CREATE TABLE old_comments (
        id TIMEUUID,
        writer TIMEUUID,
        story_id TIMEUUID,
        parent TIMEUUID,
        childs BIGINT,
        rating INT,
        date TIMESTAMP,
        subject VARCHAR,
        comment TEXT,
        PRIMARY KEY(id, date)
) 
WITH CLUSTERING ORDER BY (date DESC);

CREATE INDEX old_comments_rating
	ON old_comments(rating);

CREATE INDEX old_comments_parent
        ON old_comments(parent);

CREATE INDEX old_comments_story_id
	ON old_comments(story_id);

CREATE TABLE moderator_log (
	id TIMEUUID,
	moderator_id TIMEUUID,
	comment_id TIMEUUID,
	rating INT,
	date TIMESTAMP,
	PRIMARY KEY(id)
) WITH COMPACT STORAGE;	

CREATE TABLE stories_users (
	story_id TIMEUUID,
	nickname VARCHAR,
	PRIMARY KEY(story_id, nickname)
) WITH COMPACT STORAGE;

CREATE TABLE old_stories_users (
        story_id TIMEUUID,
	nickname VARCHAR,
	PRIMARY KEY(story_id, nickname)
) WITH COMPACT STORAGE;

CREATE TABLE comment_count (
	story_id TIMEUUID,
	rating INT,
	count INT,
	PRIMARY KEY(story_id, rating)
) WITH CLUSTERING ORDER BY (rating ASC) AND COMPACT STORAGE;

