use imywa

insert into imywa.sources(source) values ('questionnaire');

insert into imywa.dbs (db) values ('questionnaire');

insert into imywa.apps(app,dbServer,source, theme) values
	('questionnaire','localhost','questionnaire','amedita');

insert into imywa.appDbs(app, db, dbName, main) values
	('questionnaire', 'questionnaire', 'questionnaire', true);

insert into imywa.appRoles(app,role,startClass,defPermissionType) values
	('questionnaire','user','questionnaire_start','allow');

insert into imywa.userRoles(usr,app,role)	values 
	('dmelian', 'questionnaire', 'user')
	, ('root', 'questionnaire', 'user');


