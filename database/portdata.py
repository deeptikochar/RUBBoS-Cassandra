
import uuid
import datetime
import calendar
import random

def port_data():
    user_dict = port_users()
    stories_dict = port_stories('stories.data', user_dict)
    port_stories('old_stories.data', user_dict)
    port_submissions(user_dict)
    comment_dict = port_old_comments(user_dict, stories_dict)
    port_comments(user_dict, stories_dict, comment_dict)
    port_moderator_log(user_dict, comment_dict)


# Function Name: port_users
# 1. Ports users.data to users.csv
# 2. Creates a new file user_logins.csv containing nicknames, passwords, user id and access
# 3. Returns a dictionary of user ids and nicknames (used in port_stories)

def port_users():
    filename = 'users.data'
    output_filename = 'users.csv'
    output_filename1 = 'user_logins.csv'
    input_f = open(filename,'r')
    output_f = open(output_filename, 'w')
    output_f1 = open(output_filename1, 'w')
    user_dict = dict()
    for line in input_f:
        element = line.split("\t")
        date = element[8]
        try:
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d\n")
        except ValueError:
            date = '1970-01-01\n'
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        id = str(TimeUUID.convert(temp_date))
        output_line = id
        for i in range(1, len(element)-1):
            output_line = output_line + "," + element[i]
        output_line = output_line + "," + date
        output_f.write(output_line)
        output_line = element[3] +"," + element[4] + "," + id + "," + element[7]+"\n"
        output_f1.write(output_line)
        user_dict[element[0]] = (element[3],id)

    input_f.close()
    output_f.close()
    output_f1.close()
    return user_dict

# Function name: port_stories
# Called separately to port stories.data and old_stories.data to stories.csv and old_stories.csv respectively
# Uses the dictionary of user ids and nicknames
# To create a file containing nicknames, date, story id and title - user_stories.csv and user_old_stories.csv

def port_stories(filename, user_dict):

    input_f = open(filename,'r')
    temp = filename.find('.')
    output_filename = filename[0:temp] + '.csv'
    output_f = open(output_filename, 'w')
    output_filename1 = filename[0:temp] + "_users.csv"
    output_f1 = open(output_filename1, 'w')
    stories_dict = dict()
    for line in input_f:
        element = line.split("\t")
        story_id = element[0]
        date = element[3]
        try:
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        except ValueError:
            date = '1970-01-01'
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        id = str(TimeUUID.convert(temp_date))
        stories_dict[story_id] = id
        writer_id = element[4]
        if(writer_id in user_dict):
            nickname = user_dict[writer_id][0]
            output_line1 = id +",\"" + nickname +"\"\n"
            output_f1.write(output_line1)
            writer_timeuuid = user_dict[writer_id][1]

        output_line = id + ",\"" + element[1] + "\",\"" + element[2] + "\"," + date + "," + writer_timeuuid +","+element[5]
        output_f.write(output_line)

    input_f.close()
    output_f.close()
    output_f1.close()
    return stories_dict

# Function name: port_submissions
# Ports submissions.data to submissions.csv

def port_submissions(user_dict):
    input_f = open('submissions.data', 'r')
    output_f = open('submissions.csv', 'w')

    for line in input_f:
        element = line.split("\t")
        date = element[3]
        try:
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        except ValueError:
            date = '1970-01-01'
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        id = str(TimeUUID.convert(temp_date))
        writer_id = element[4]

        if(writer_id in user_dict):
            writer_timeuuid = user_dict[writer_id][1]

        output_line = id + ",\"" + element[1] + "\",\"" + element[2] + "\"," + date + "," + writer_timeuuid +","+element[5]
        output_f.write(output_line)

    input_f.close()
    output_f.close()

# Function name: port_moderator_log
# Ports moderator_log.data to moderator_log.csv

def port_moderator_log(user_dict, comment_dict):
    filename = 'moderator_log.data'
    output_filename = 'moderator_log.csv'
    input_f = open(filename,'r')
    output_f = open(output_filename, 'w')
    for line in input_f:
        element = line.split("\t")
        temp_date = datetime.datetime.now()
        id = str(TimeUUID.convert(temp_date))
        moderator_id = element[1]
        if moderator_id in user_dict:
            moderator_timeuuid = user_dict[moderator_id][1]
        else:
            continue
        comment_id = element[2]
        if comment_id in comment_dict:
            comment_timeuuid = comment_dict[comment_id]

        output_line = id +"," + moderator_timeuuid + "," + comment_timeuuid + "," + element[3]
        output_f.write(output_line)
    input_f.close()
    output_f.close()

# Function name: port_comments
# 1. Ports comments.data to comments.csv
# 2. Creates a new file comment_count.csv containing story ids, ratings and comment counts

def port_comments(user_dict, stories_dict, comment_dict):
    filename = 'comments.data'
    output_filename = 'comments.csv'
    input_f = open(filename, 'r')
    output_f = open(output_filename, 'w')
    count_comments = dict()
    for line in input_f:
        line = line.replace('\n', '')
        element = line.split("\t")
        date = element[6]
        try:
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        except ValueError:
            date = '1970-01-01'
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        id = str(TimeUUID.convert(temp_date))
        if element[1] in user_dict:
            writer_timeuuid = user_dict[element[1]][1]
        else:
            writer_timeuuid = ''
        if element[2] in stories_dict:
            story_timeuuid = stories_dict[element[2]]

        if element[3] in comment_dict:
            parent_timeuuid = comment_dict[element[3]]
        else:
            parent_timeuuid = '11111111-1111-1111-1111-111111111111'
        output_line =  id+","+ writer_timeuuid +","+story_timeuuid+","+parent_timeuuid+","+element[4]+","+element[5]+","+date + ",\"" + element[7] + "\",\"" + element[8] + "\"\n"
        output_f.write(output_line)
        tuple1 = (story_timeuuid, element[5])
        if tuple1 in count_comments:
            count_comments[tuple1] = count_comments[tuple1] + 1
        else:
            count_comments[tuple1] = 1
    input_f.close()
    output_f.close()

    output_filename = 'comment_count.csv'
    output_f = open(output_filename, 'w')
    for item in count_comments:
        count = count_comments[item]
        output_line = str(item[0]) +","+str(item[1])+","+str(count)+"\n"
        output_f.write(output_line)
    output_f.close()

# Function name: port_old_comments
# Ports old_comments.data to old_comments.csv

def port_old_comments(user_dict, stories_dict):
    filename = 'old_comments.data'
    output_filename = 'old_comments.csv'
    input_f = open(filename, 'r')
    output_f = open(output_filename, 'w')
    comment_dict = dict()
    for line in input_f:
        line = line.replace('\n', '')
        element = line.split("\t")
        date = element[6]
        try:
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        except ValueError:
            date = '1970-01-01'
            temp_date = datetime.datetime.strptime(date, "%Y-%m-%d")
        id = str(TimeUUID.convert(temp_date))
        comment_dict[element[0]] = id

        if element[1] in user_dict:
            writer_timeuuid = user_dict[element[1]][1]
        else:
            writer_timeuuid = ''

        if element[2] in stories_dict:
            story_timeuuid = stories_dict[element[2]]
        else:
            story_timeuuid = ''

        if element[3] in comment_dict:
            parent_timeuuid = comment_dict[element[3]]
        else:
            parent_timeuuid = '11111111-1111-1111-1111-111111111111'

        output_line =  id+","+ writer_timeuuid +","+story_timeuuid+","+parent_timeuuid+","+element[4]+","+element[5]+","+date + ",\"" + element[7] + "\",\"" + element[8] + "\"\n"
        output_f.write(output_line)
    input_f.close()
    output_f.close()
    return comment_dict

def utctime():
    """
    Generate a timestamp from the current time in UTC
    """
    d = datetime.datetime.utcnow()
    return mkutime(d)

def mkutime(d):
    return float(calendar.timegm(d.timetuple())) + float(d.microsecond) / 1e6


class TimeUUID(uuid.UUID):
    """
    A class that makes dealing with time and V1 UUIDs much easiser. Offers
    accurate comparison (compares accurately with other TimeUUIDs and UUIDv1),
    a way to get TimeUUIDs with UTC-timestamps (instead of the default
    localtime), an easy way to get the UTC datetime object from it, and
    default encoding and decoding methods.
    """

    @classmethod
    def convert(cls, value, randomize=True, lowest_val=False):
        """
        Try to convert a datetime types to a TimeUUID.

        """

        if isinstance(value, datetime.datetime):
            return cls.with_timestamp(mkutime(value))
        else:
            raise ValueError("Sorry, I don't know how to convert {} to a "
                             "TimeUUID".format(value))

    @classmethod
    def with_timestamp(cls, timestamp):
        """
        Create a TimeUUID with any timestamp. Here be dragons. No guarantees.
        `randomize` will create a UUID with randomish bits.

        """
        ns = timestamp * 1e9
        ts = int(ns // 100) + 0x01b21dd213814000
        time_low = ts & 0xffffffff
        time_mid = (ts >> 32) & 0xffff
        time_hi_version = (ts >> 48) & 0x0fff

        cs = random.randrange(1<<14)
        clock_seq_low = cs & 0xff
        clock_seq_hi_variant = (cs >> 8) & 0x3f
        node = uuid.getnode()

        return cls(
            fields=(time_low, time_mid, time_hi_version,
                    clock_seq_hi_variant, clock_seq_low, node),
            version=1
        )


port_data()