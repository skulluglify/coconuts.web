#!/usr/bin/env python3

from backends.DBs.Core \
    import DBConnect, \
    DBTable, \
    DBVar, \
    DBShowTables, \
    DBLink, \
    DBCreateTable, \
    DBVarType, \
    DBEnum, DBInsertTable, DBValue, DBColumnTable, DBUpdateTable

if str(__name__).upper() in ("__MAIN__",):
    db = DBConnect(
        DB_USER="coconuts",
        DB_PASSWORD="coconuts",
        DB_NAME="coconuts"
    )

    db.connect()

    # DBLink(
    #     db,
    #     DBDropTable(
    #         DBTable("user"),
    #         ignore_table_created=True
    #     )
    # ).exec()

    # user = DBTable("guest")
    # achievements = DBTable("achievements")
    # user_id = DBVar(
    #     "user_id",
    #     DBVarType.INT(DBEnum.DB_AUTO_RESIZE),
    #     DBEnum.DB_PRIMARY_KEY,
    #     DBEnum.DB_AUTO_INCREMENT,
    #     DBEnum.DB_NOT_NULL
    # )
    # user_unique_name = DBVar("user_unique_name", *DBTextTypeNotNull)
    # value_id = DBValue(user_id, 12)
    #
    # print(
    #     user_id.to_str()
    # )
    #
    # print(
    #     value_id.to_str()
    # )
    print(DBShowTables(db).to_list())

    users = DBTable("users")

    user_id = DBVar(
        "user_id",
        DBVarType.INT(11),
        DBEnum.DB_PRIMARY_KEY,
        DBEnum.DB_AUTO_INCREMENT,
        DBEnum.DB_NOT_NULL
    )

    user_uniq_name = DBVar(
        "user_uniq_name"
    )

    # DBLink(
    #     db,
    #     DBDropTable(
    #         users,
    #         ignore_table_created=True
    #     )
    # ).exec()

    try:

        DBLink(
            db,
            DBCreateTable(
                users,
                user_id
            )
        ).exec()

    except Exception as err:

        pass

    try:
        DBLink(
            db,
            DBColumnTable(
                users,
                DBColumnTable.New,
                user_uniq_name
            )
        ).exec()
    except Exception as err:

        pass

    insert = DBInsertTable(
        users,
        DBValue(
            user_uniq_name,
            "asy_0x0"
        ),
        DBValue(
            user_id,
            6
        ),
        # IGNORE_CHECKER=True
    )

    DBUpdateTable(
        users,
        DBValue(
            user_uniq_name,
            "asy_1_1"
        ),
        WHERES=[
            DBValue(
                user_uniq_name,
                "asy_0x0"
            )
        ]
    )

    print(insert.to_str())
    print(insert.PARAMS)

    DBLink(
        db,
        insert
    )#.exec()

    print(
        db.fetch("SELECT * FROM `users`")
    )

    # DBLink(
    #     db,
    #     DBCreateTable(
    #         user,
    #         user_id,
    #         user_unique_name
    #     )
    # ).exec()

    db.close()
