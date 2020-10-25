-- TODO: Put ALL SQL in between `BEGIN TRANSACTION` and `COMMIT`
BEGIN TRANSACTION;

-- TODO: create tables

-- CREATE TABLE `examples` (
-- 	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
-- 	`name`	TEXT NOT NULL
-- );
CREATE TABLE images (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    file_ext TEXT NOT NULL,
    author TEXT,
    description TEXT
);

-- TODO: initial seed data
INSERT INTO images (id, file_ext, author, description) VALUES (1, 'jpg', 'Ansel Adams', 'Hey guys, Ansel Adams here. Just wanted to post my first picture on this board. Dont judge too much.');
INSERT INTO images (id, file_ext, author, description) VALUES (2, 'jpg', 'John Smith', 'My first post');
INSERT INTO images (id, file_ext, author, description) VALUES (3, 'png', 'Aidan Sisk', 'Figured Id post this sketch Ive been working on.');
INSERT INTO images (id, file_ext, author, description) VALUES (4, 'jpg', 'Bob John', 'Beginner watercolor painting');
INSERT INTO images (id, file_ext, author, description) VALUES (5, 'jpg', 'Phil Sup', '');
INSERT INTO images (id, file_ext, author, description) VALUES (6, 'jpg', 'hey123', 'Thought id post, why not');
INSERT INTO images (id, file_ext, author, description) VALUES (7, 'jpg', 'Catherine', 'Stumbled across a  bee');
INSERT INTO images (id, file_ext, author, description) VALUES (8, 'jpg', 'Catherine', 'Cool cloudy day');
INSERT INTO images (id, file_ext, author, description) VALUES (9, 'jpg', 'wassup923', 'yolo');
INSERT INTO images (id, file_ext, author, description) VALUES (10, 'jpg', '', '');
-- INSERT INTO `examples` (id,name) VALUES (1, 'example-1');
-- INSERT INTO `examples` (id,name) VALUES (2, 'example-2');


CREATE TABLE tags (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    tag TEXT NOT NULL UNIQUE
);

INSERT INTO tags (id, tag) VALUES (1, 'photography');
INSERT INTO tags (id, tag) VALUES (2, 'painting');
INSERT INTO tags (id, tag) VALUES (3, 'drawing');
INSERT INTO tags (id, tag) VALUES (4, 'beginner');
INSERT INTO tags (id, tag) VALUES (5, 'watercolor');

CREATE TABLE image_tags (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    image_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL
);

INSERT INTO image_tags (id, image_id, tag_id) VALUES (1, 1, 1);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (2, 2, 2);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (3, 3, 3);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (4, 4, 2);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (5, 4, 4);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (6, 4, 5);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (7, 5, 3);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (8, 6, 3);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (9, 7, 1);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (10, 7, 4);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (11, 8, 1);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (12, 8, 4);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (13, 9, 2);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (14, 9, 4);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (15, 10, 2);

COMMIT;
