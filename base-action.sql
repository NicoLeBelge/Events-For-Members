-- add dummy club id=0
INSERT INTO clubs (club_id, region, city, name) VALUES (0, 'FRA', '--', 'en attente d'affectation');

-- rename birthdate → upd_date
ALTER TABLE members RENAME COLUMN birthdate TO upd_date;

-- add column m_owner in member 
ALTER TABLE members ADD COLUMN m_owner mediumint(8) unsigned ;

