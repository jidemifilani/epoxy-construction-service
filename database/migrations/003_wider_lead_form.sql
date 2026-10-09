-- Landing pages record their own name in the form column ("landing-<page slug>"),
-- which is longer than the original 20 characters.
ALTER TABLE leads MODIFY form VARCHAR(60) NOT NULL;
