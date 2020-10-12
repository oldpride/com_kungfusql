UPDATE #__advpoll_answers
   SET votes = votes - 1
WHERE id in ( select
                 SUBSTRING_INDEX(SUBSTRING_INDEX(onepolluser.answer_ids, ', ', numbers.n), ', ', -1)
              from
                 (select 1 n union all
                  select 2 union all select 3 union all
                  select 4 union all select 5 union all
                  select 6 union all select 7 union all
                  select 8 union all select 9
                 ) numbers INNER JOIN (
                  select logs.*
                  from   #__advpoll_logs logs, #__advpoll_polls polls
                  where
                         polls.title = 'kungfusql_param1'
                         and logs.user_id = '$user->id'
                         and logs.poll_id = polls.id
                ) onepolluser
                  on CHAR_LENGTH(onepolluser.answer_ids)
                    -CHAR_LENGTH(REPLACE(onepolluser.answer_ids, ', ', ''))>=(numbers.n-1)*2
     )
     and votes > 0

DELETE
FROM #__advpoll_logs
WHERE
   poll_id = (
      select polls.id from #__advpoll_polls polls
      where polls.title = 'kungfusql_param1'
   )
and user_id = '$user->id'

