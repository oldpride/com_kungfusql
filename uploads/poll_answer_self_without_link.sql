select split_log.user_id as user_id, #__users.name as user_name, split_log.answer_ids,
   split_log.answer_id, extracted_answers.answer_title,
   split_log.poll_id, #__advpoll_polls.title as poll_title,
   -- split_log.date, split_log.country_code
   convert_tz(split_log.date, '+00:00','-05:00') as date, split_log.country_code
from
   (
      select
         onepoll_oneuser.*,
         SUBSTRING_INDEX(SUBSTRING_INDEX(onepoll_oneuser.answer_ids, ', ', numbers.n), ', ', -1) answer_id
      from
         (
            select 1 n union all
            select 2 union all select 3 union all
            select 4 union all select 5 union all
            select 6 union all select 7 union all
            select 8 union all select 9
         ) numbers INNER JOIN (
            select * from #__advpoll_logs where poll_id = 'kungfusql_param1' and user_id = '$user->id') onepoll_oneuser
            on CHAR_LENGTH(onepoll_oneuser.answer_ids) 
               -CHAR_LENGTH(REPLACE(onepoll_oneuser.answer_ids, ', ', ''))>=(numbers.n-1)*2
         ) split_log,
         #__users,
         (
         -- use this if answer is a link
         -- select id, ExtractValue(SUBSTRING(title, 1, INSTR(title, '</p>') + 4), '/p[1]') as answer_title
         -- use this if answer is just a string
         select id, title as answer_title
         from #__advpoll_answers
   ) extracted_answers,
   #__advpoll_polls
   where
      split_log.user_id = #__users.id
      and split_log.answer_id = extracted_answers.id
      and split_log.poll_id = #__advpoll_polls.id
      order by
      date desc
