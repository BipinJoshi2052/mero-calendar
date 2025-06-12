transactions

id INTEGER primary auto-increment
user_id
title varchar not null
type INTEGER not null
category_id INTEGER
sub_category_id INTEGER
amount FLOAT
month_value INTEGER
date_value INTEGER
created_at datetime current-timestamp
updated_at datetime current-timestamp

index
user_id,month_value
user_id,month_value,date_value

foreign key relation
category_id - category - id
sub_category_id - sub_category id
user_id - users - id



category 
id INTEGER primary auto-increment
title varchar not null
created_at datetime current-timestamp
updated_at datetime current-timestamp


sub_category
id INTEGER primary auto-increment
title varchar not null
category_id INTEGER not null
created_at datetime current-timestamp
updated_at datetime current-timestamp

foreign key relation
category_id - category - id



events
id INTEGER primary auto-increment
title varchar not null
month_value INTEGER
date_value INTEGER
created_at datetime current-timestamp
updated_at datetime current-timestamp