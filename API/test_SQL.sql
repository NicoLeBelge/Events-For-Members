SELECT subevents.name
FROM subevents
INNER JOIN registrations
ON subevents.id = registrations.subevent_id
WHERE registrations.id=8;

SELECT registrations.member_id, subevents.name
FROM registrations
INNER JOIN subevents
ON subevents.id = registrations.subevent_id
WHERE registrations.id=8;

SELECT registrations.member_id, subevents.name, events.name
FROM registrations
INNER JOIN subevents
ON subevents.id = registrations.subevent_id
INNER JOIN events
ON events.id = subevents.event_id
WHERE registrations.id=79;







INNER JOIN events
ON events.id = subevents.event_id;