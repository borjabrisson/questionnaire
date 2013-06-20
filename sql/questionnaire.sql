create table if not exists questionnaire(
	id integer AUTO_INCREMENT primary key,
	description varchar(30)
) engine InnoDB, default character set utf8;

create table if not exists questions(
	id integer AUTO_INCREMENT primary key,
	question varchar(30),
	language varchar(2),
	multivalue boolean default false
) engine InnoDB, default character set utf8;


create table if not exists questionByquestionnaire(
	questionnaire integer,
	question integer,
	level integer,
	primary key (questionnaire,question),
	foreign key (questionnaire) references questionnaire(id) on delete RESTRICT on update cascade,
	foreign key (question) references questions(id) on delete RESTRICT on update cascade
) engine InnoDB, default character set utf8;


create table if not exists historic(
	hist integer AUTO_INCREMENT primary key,
	questionnaire integer,
	creationTime datetime,
	user varchar(15),
	location varchar(15),
	foreign key (questionnaire) references questionnaire(id) on delete RESTRICT on update cascade
) engine InnoDB, default character set utf8;

create table if not exists answerByquestion(
	question integer,
	answer integer default 1,
	caption varchar(30),
	level integer default 1,
	primary key (question,answer),
	foreign key (question) references questions(id) on delete RESTRICT on update cascade
) engine InnoDB, default character set utf8;


create table if not exists answerRecord(
	hist integer ,
	question integer,
	answer varchar(120),
	primary key (hist,question),
	foreign key (hist) references historic(hist) on delete RESTRICT on update cascade,
	foreign key (question) references questions(id) on delete RESTRICT on update cascade
) engine InnoDB, default character set utf8;









