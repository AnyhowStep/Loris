INSERT INTO LorisMenu (Label, Link, Parent, OrderNumber) VALUES
    ('Issue Tracker Default Assignee', 'issue_tracker_default_assignee/', (SELECT ID FROM LorisMenu as L WHERE Label='Admin'), 7);

INSERT INTO LorisMenuPermissions (MenuID, PermID)
    SELECT m.ID, p.PermID FROM permissions p CROSS JOIN LorisMenu m WHERE p.code='superuser' AND m.Label='Issue Tracker Default Assignee';
