use rubbos;
COPY users (id, firstname , lastname , nickname , password , email , rating , access , creation_date ) FROM 'users.csv';
COPY stories (id, title, body, date, writer, category) FROM 'stories.csv';
COPY old_stories (id, title, body, date, writer, category) FROM 'old_stories.csv';
COPY submissions (id, title, body, date, writer, category) FROM 'submissions.csv';
COPY comments (id, writer, story_id, parent, childs, rating, date, subject, comment) FROM 'comments.csv';
COPY old_comments (id, writer, story_id, parent, childs, rating, date, subject, comment) FROM 'old_comments.csv';
COPY moderator_log (id, moderator_id, comment_id, rating) FROM 'moderator_log.csv';
COPY user_logins (nickname, password, id, access) FROM 'user_logins.csv';
COPY stories_users (story_id, nickname) FROM 'stories_users.csv';
COPY old_stories_users (story_id, nickname) FROM 'old_stories_users.csv';
COPY comment_count (story_id, rating, "count") FROM 'comment_count.csv';

