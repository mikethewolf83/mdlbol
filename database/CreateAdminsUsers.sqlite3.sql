CREATE TABLE "mb_normal_user" (
	"id"    INTEGER PRIMARY KEY AUTOINCREMENT,
	"username"	TEXT NOT NULL UNIQUE
);

CREATE TABLE "mb_admin_user" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT,
	"username"	TEXT NOT NULL UNIQUE
);

INSERT INTO "mb_admin_user" (
    "username"
) VALUES (
    "admin"
);

INSERT INTO "mb_admin_user" (
    "username"
) VALUES (
    "angel.perez"
);

INSERT INTO "mb_admin_user" (
    "username"
) VALUES (
    "maikel.nieves"
);

INSERT INTO "mb_normal_user" (
    "username"
) VALUES (
    "angel.perez"
);

INSERT INTO "mb_normal_user" (
    "username"
) VALUES (
    "maikel.nieves"
);