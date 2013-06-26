delimiter $$

drop procedure if exists hist_create$$
create procedure hist_create(
	in iquestionnaire integer
)
begin
	insert into historic (questionnaire,creationTime,user) values (iquestionnaire,now(),substring_index(user(),'@',1));

	select 0 as error;
end$$

drop procedure if exists hist_answerInsert$$
create procedure hist_answerInsert(
	in iquestion integer,
	in ianswer varchar (120)
)
begin
	declare ihist integer;
	select max(hist) into ihist from historic where user = substring_index(user(),'@',1);
	insert into answerRecord (hist,question,answer) values (ihist,iquestion,ianswer);

	select 0 as error;
end$$

delimiter ;