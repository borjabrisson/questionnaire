delimiter $$

drop procedure if exists workDay_open$$
create procedure workDay_open()
begin
	declare iworkday date;
	select workDay into iworkday from pos;

	if iworkday is null then
		set iworkday = curdate();
		insert into workDay (workDay,opening) values (iworkday,now());
		update pos set workDay=iworkday where id=1;

		select 0 as error;
	else
		select 1 as error, 'El día ya se encuentra abierto' as message;
	end if;
end$$

delimiter ;