-- Associate text that each user is looking at
CREATE TABLE text (
    text_user INT UNSIGNED NOT NULL,
    text_text TEXT,

    PRIMARY KEY(text_user)
)
