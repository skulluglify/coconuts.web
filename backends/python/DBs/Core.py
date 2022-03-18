#!/usr/bin/env python3
import os
import types
import typing
import mysql.connector
import sqlite3


class DBEnum(object):
    DB_AUTO_RESIZE: int = 0b00000001
    DB_PRIMARY_KEY: int = 0b00000010
    DB_AUTO_INCREMENT: int = 0b00000100
    DB_NOT_NULL: int = 0b00001000
    DB_IF_EXISTS: int = 0b00010000


class DBConnect(object):
    # mysql only
    DB_HOST: str = "127.0.0.1"
    DB_PORT: int = 3306
    DB_USER: str
    DB_PASSWORD: str
    # url or path
    DB_PREFIX: str = os.getcwd()
    DB_SUFFIX: str = ".db"
    DB_NAME: str
    # mysql or sqlite
    DB_CONNECT: mysql.connector.connection.MySQLConnectionAbstract | sqlite3.Connection
    DB_CURSOR: mysql.connector.connection.CursorBase | sqlite3.Cursor
    # is sqlite
    DB_SQLITE: bool

    def __init__(self, **kwargs):

        self.__dict__.update(kwargs)

    def connect(self):

        if getattr(self, "DB_USER", None) or getattr(self, "DB_PASSWORD", None):

            self.mysql_connect()
            self.DB_SQLITE = False

        else:

            self.sqlite_connect()
            self.DB_SQLITE = True

        return self

    def mysql_connect(self):

        try:

            self.DB_CONNECT = mysql.connector.connection.MySQLConnection(

                user=self.DB_USER,
                password=self.DB_PASSWORD,
                host=self.DB_HOST,
                port=self.DB_PORT,
                database=self.DB_NAME,
                raise_on_warnings=True,
                use_pure=False
            )

            self.DB_CURSOR = self.DB_CONNECT.cursor(
                raw=True,
                buffered=True
            )

        except Exception as err:

            print(err)

    def sqlite_connect(self):

        try:

            self.DB_CONNECT = sqlite3.connect(
                database=os.path.join(
                    self.DB_PREFIX,
                    "{DB_NAME}{DB_SUFFIX}".format(
                        DB_NAME=self.DB_NAME,
                        DB_SUFFIX=self.DB_SUFFIX
                    )
                ),
                timeout=5
            )

            self.DB_CONNECT.row_factory = sqlite3.Row
            self.DB_CONNECT.text_factory = bytearray
            self.DB_CURSOR = self.DB_CONNECT.cursor()

        except Exception as err:

            print(err)

    def fetch(self, executes: str, params: typing.Tuple[typing.Any] = tuple()):

        # params = params or tuple()
        if not self.DB_SQLITE:
            executes = executes.replace("?", "%s")

        if params:
            # print(executes, params, "EXECUTES")
            self.DB_CURSOR.execute(executes, params)
        else:
            self.DB_CURSOR.execute(executes)

        try:

            data: typing.Any = self.DB_CURSOR.fetchall()

        except Exception as err:

            data: typing.Any = self.DB_CURSOR.fetchone()

        if data:
            return [
                *(
                        data or []
                )
            ]
        return None

    def close(self):

        self.DB_CURSOR.close()
        self.DB_CONNECT.commit()
        self.DB_CONNECT.close()

    def __enter__(self):

        return self

    def __exit__(self, exc_type, exc_val, exc_tb):

        self.close()


class DBObject(object):
    CONTEXT: str

    def __init__(self):
        self.CONTEXT = ""

    def to_str(self) -> str:
        return self.CONTEXT


class DBProgram(DBObject):
    pass


class DBVarTypeEnum(object):
    INT: int = 0
    REAL: int = 1
    CHAR: int = 2
    VARCHAR: int = 3
    TEXT: int = 4
    BLOB: int = 5

    # BINARY: int = 6
    # VARBINARY: int = 7
    # TINYTEXT: int = 8
    # TINYBLOB: int = 9
    # MEDIUMTEXT: int = 10
    # MEDIUMBLOB: int = 11
    # LONGTEXT: int = 12
    # LONGBLOB: int = 13
    # ENUM: int = 14
    # SET: int = 15

    def __init__(self):
        super().__init__()


class DBVarTypeStruct(object):
    VAR_TYPE: int
    VAR_SIZE: int | types.NoneType
    VAR_PARAMS: typing.Tuple[str, ...]

    def __init__(self, var_type: int, var_size: int = 0, *args: str, **kwargs):
        super().__init__()
        self.__dict__.update(kwargs)
        self.VAR_TYPE = var_type
        self.VAR_SIZE = var_size
        if args:
            self.VAR_PARAMS = args

    def vt_to_str(self):
        match self.VAR_TYPE:

            case (DBVarTypeEnum.INT):
                return "INT"

            case (DBVarTypeEnum.REAL):
                return "REAL"

            case (DBVarTypeEnum.CHAR):
                return "CHAR"

            case (DBVarTypeEnum.VARCHAR):
                return "VARCHAR"

            case (DBVarTypeEnum.TEXT):
                return "TEXT"

            case (DBVarTypeEnum.BLOB):
                return "BLOB"

            # case (DBVarTypeEnum.BINARY):
            #     return "BINARY"
            #
            # case (DBVarTypeEnum.VARBINARY):
            #     return "VARBINARY"
            #
            # case (DBVarTypeEnum.TINYBLOB):
            #     return "TINYBLOB"
            #
            # case (DBVarTypeEnum.TINYTEXT):
            #     return "TINYTEXT"
            #
            # case (DBVarTypeEnum.MEDIUMBLOB):
            #     return "MEDIUMBLOB"
            #
            # case (DBVarTypeEnum.MEDIUMTEXT):
            #     return "MEDIUMTEXT"
            #
            # case (DBVarTypeEnum.LONGBLOB):
            #     return "LONGBLOB"
            #
            # case (DBVarTypeEnum.LONGTEXT):
            #     return "LONGTEXT"
            #
            # case (DBVarTypeEnum.ENUM):
            #     return "ENUM"
            #
            # case (DBVarTypeEnum.SET):
            #     return "SET"

    def to_str(self):
        params: typing.Tuple[str, ...] = getattr(self, "VAR_PARAMS", [])
        if len(params) > 0:
            return "{VAR_TYPE}({VAR_VALUES})".format(
                VAR_TYPE=self.vt_to_str(),
                VAR_VALUES=", ".join(params)
            )
        # bypass DBEnum.DB_AUTO_RESIZE
        if self.VAR_SIZE > 1:
            return "{VAR_TYPE}({VAR_SIZE})".format(
                VAR_TYPE=self.vt_to_str(),
                VAR_SIZE=self.VAR_SIZE
            )
        return self.vt_to_str()


class DBVarType(object):
    CHAR: DBVarTypeStruct = DBVarTypeStruct(DBVarTypeEnum.CHAR)

    # TINYTEXT: DBVarTypeStruct = DBVarTypeStruct(DBVarTypeEnum.TINYTEXT)
    # TINYBLOB: DBVarTypeStruct = DBVarTypeStruct(DBVarTypeEnum.TINYBLOB)
    # MEDIUMTEXT: DBVarTypeStruct = DBVarTypeStruct(DBVarTypeEnum.MEDIUMTEXT)
    # MEDIUMBLOB: DBVarTypeStruct = DBVarTypeStruct(DBVarTypeEnum.MEDIUMBLOB)
    # LONGTEXT: DBVarTypeStruct = DBVarTypeStruct(DBVarTypeEnum.LONGTEXT)
    # LONGBLOB: DBVarTypeStruct = DBVarTypeStruct(DBVarTypeEnum.LONGBLOB)

    @staticmethod
    def INT(size: int | types.NoneType) -> DBVarTypeStruct:
        return DBVarTypeStruct(DBVarTypeEnum.INT, size)

    @staticmethod
    def REAL(size: int | types.NoneType) -> DBVarTypeStruct:
        return DBVarTypeStruct(DBVarTypeEnum.REAL, size)

    @staticmethod
    def VARCHAR(size: int | types.NoneType) -> DBVarTypeStruct:
        return DBVarTypeStruct(DBVarTypeEnum.VARCHAR, size)

    # @staticmethod
    # def BINARY(size: int | types.NoneType) -> DBVarTypeStruct:
    #     return DBVarTypeStruct(DBVarTypeEnum.BINARY, size)
    #
    # @staticmethod
    # def VARBINARY(size: int | types.NoneType) -> DBVarTypeStruct:
    #     return DBVarTypeStruct(DBVarTypeEnum.VARBINARY, size)

    @staticmethod
    def TEXT(size: int | types.NoneType) -> DBVarTypeStruct:
        return DBVarTypeStruct(DBVarTypeEnum.TEXT, size)

    @staticmethod
    def BLOB(size: int | types.NoneType) -> DBVarTypeStruct:
        return DBVarTypeStruct(DBVarTypeEnum.BLOB, size)

    # @staticmethod
    # def ENUM(*values: str) -> DBVarTypeStruct:
    #     assert values
    #
    #     return DBVarTypeStruct(DBVarTypeEnum.ENUM, 0, *values)
    #
    # @staticmethod
    # def SET(*values: str) -> DBVarTypeStruct:
    #     assert values
    #
    #     return DBVarTypeStruct(DBVarTypeEnum.SET, 0, *values)


class DBVar(DBObject):
    VAR_NAME: str
    VAR_STRUCTURE: DBVarTypeStruct
    VAR_NULLABLE: bool
    VAR_PRIMARY_KEY: bool
    VAR_AUTO_INCREMENT: bool

    def __init__(self, var_name: str, var_type: DBVarTypeStruct = DBVarType.TEXT(DBEnum.DB_AUTO_RESIZE), *rules: int):
        super().__init__()

        assert var_name
        assert var_type

        self.VAR_NAME = var_name
        self.VAR_STRUCTURE = var_type
        self.VAR_NULLABLE = True
        self.VAR_PRIMARY_KEY = False
        self.VAR_AUTO_INCREMENT = False

        context_rules: str = ""
        params: typing.List[int, ...] = [*rules]

        # ignore
        if DBEnum.DB_AUTO_RESIZE in params:
            # params.remove(DBEnum.DB_AUTO_RESIZE)
            raise Exception("Conflict using DB_AUTO_RESIZE!")

        if DBEnum.DB_IF_EXISTS in params:
            # params.remove(DBEnum.DB_IF_EXISTS)
            raise Exception("Conflict using DB_IF_EXISTS!")

        if DBEnum.DB_NOT_NULL in params:
            # params.remove(DBEnum.DB_NOT_NULL)
            self.VAR_NULLABLE = False
            context_rules += " NOT NULL"

        if DBEnum.DB_PRIMARY_KEY in params:
            # using AUTO_INCREMENT only with PRIMARY_KEY
            if DBEnum.DB_AUTO_INCREMENT in params:
                # params.remove(DBEnum.DB_AUTO_INCREMENT)
                self.VAR_AUTO_INCREMENT = True
                context_rules += " AUTO_INCREMENT"

            # params.remove(DBEnum.DB_PRIMARY_KEY)
            self.VAR_PRIMARY_KEY = True
            context_rules += ", PRIMARY KEY({VAR_NAME})".format(
                VAR_NAME=self.VAR_NAME
            )
        else:
            if DBEnum.DB_AUTO_INCREMENT in params:
                raise Exception("Conflict using DB_AUTO_INCREMENT! Only for DB_PRIMARY_KEY!")

        self.CONTEXT = "{VAR_NAME} {VAR_TYPE}{VAR_RULES}".format(
            VAR_NAME=self.VAR_NAME,
            VAR_TYPE=self.VAR_STRUCTURE.to_str(),
            VAR_RULES=context_rules
        )


class DBTable(DBObject):
    TABLE_NAME: str
    TABLE_CREATED: bool
    TABLE_VARS: typing.List[DBVar]

    def __init__(self, table_name: str):
        super().__init__()
        self.TABLE_CREATED = False
        if table_name:
            self.TABLE_NAME = table_name
            self.CONTEXT = table_name
        else:
            raise Exception("Undefined TABLE!")


class DBCreateTable(DBProgram):
    # declares: typing.Tuple[DBObject, ...]
    TABLE_PRIMARY_KEY: str
    TABLE_CONTAINS_PRIMARY_KEY: bool

    def __init__(self, table: DBTable, *var_objs: DBVar):
        super().__init__()
        if table:
            if not table.TABLE_CREATED:
                self.CONTEXT = "CREATE TABLE IF NOT EXISTS `{TABLE_NAME}`".format(

                    TABLE_NAME=table.to_str()
                )
                table.TABLE_CREATED = True
                table.TABLE_VARS = [
                    *(
                            var_objs or []
                    )
                ]
            else:
                raise Exception("TABLE has been created!")
        else:
            raise Exception("Undefined TABLE!")

        params: typing.List[DBVar, ...] = [*var_objs]
        self.TABLE_PRIMARY_KEY = ""
        self.CONTAINS_PRIMARY_KEY = False

        if len(params) > 0:

            c: str
            i: int = 0
            n: int = len(params)
            context: str = "( "

            for var_obj in params:

                if var_obj.VAR_PRIMARY_KEY:

                    context_rules: str = ""
                    self.TABLE_PRIMARY_KEY = var_obj.VAR_NAME
                    self.CONTAINS_PRIMARY_KEY = True

                    if not var_obj.VAR_NULLABLE:
                        context_rules += " NOT NULL"

                    if var_obj.VAR_AUTO_INCREMENT:
                        context_rules += " AUTO_INCREMENT"

                    context += "{VAR_NAME} {VAR_TYPE}{VAR_RULES}".format(
                        VAR_NAME=var_obj.VAR_NAME,
                        VAR_TYPE=var_obj.VAR_STRUCTURE.to_str(),
                        VAR_RULES=context_rules
                    )
                    c = ", " if not n <= i + 1 else ""
                    context += c
                    i += 1
                    continue

                c = ", " if not n <= i + 1 else ""
                context += var_obj.to_str() + c
                i += 1

            if self.CONTAINS_PRIMARY_KEY:
                context += ", PRIMARY KEY({VAR_NAME})".format(
                    VAR_NAME=self.TABLE_PRIMARY_KEY
                )

            context += " )"

            self.CONTEXT += context


class DBDropTable(DBProgram):
    DROP_TABLE: bool
    TABLE_NAME: str

    def __init__(self, table: DBTable, ignore_table_created: bool = False):
        super().__init__()
        self.DROP_TABLE = False
        if table:
            if table.TABLE_CREATED or ignore_table_created:
                self.DROP_TABLE = True
                self.TABLE_NAME = table.TABLE_NAME
                self.CONTEXT = "DROP TABLE IF EXISTS `{TABLE_NAME}`".format(

                    TABLE_NAME=table.TABLE_NAME
                )
                table.TABLE_CREATED = False
            else:
                raise Exception("TABLE not created anymore!")
        else:
            raise Exception("Undefined TABLE!")


class DBMoveTable(DBProgram):
    TABLE_NAME: str
    TABLE_NEW_NAME: str

    def __init__(self, table: DBTable, table_new: DBTable, ignore_table_created: bool = False):
        super().__init__()
        if table:
            if table.TABLE_CREATED or ignore_table_created:
                if not table_new.TABLE_CREATED:
                    self.TABLE_NAME = table.TABLE_NAME
                    self.TABLE_NEW_NAME = table_new.TABLE_NAME
                    table.TABLE_NAME = self.TABLE_NEW_NAME
                    self.CONTEXT = "ALTER TABLE IF EXISTS `{TABLE_NAME}` RENAME TO `{TABLE_NEW_NAME}`".format(

                        TABLE_NAME=table.to_str(),
                        TABLE_NEW_NAME=table_new.to_str()
                    )
                else:
                    raise Exception("Conflict with TABLE NEW name! TABLE NEW has been created!")
            else:
                raise Exception("TABLE not created anymore!")
        else:
            raise Exception("Undefined TABLE!")


class DBColumnTable(DBProgram):
    Del: int = 0
    New: int = 1
    TABLE_NAME: str
    OPERATION: int
    VAR_RULES: typing.List[DBVar]

    def __init__(self, table: DBTable, op: int, *var_objs: DBVar):
        super().__init__()
        if table:
            if table.TABLE_CREATED:
                self.TABLE_NAME = table.TABLE_NAME
                self.OPERATION = op
                self.VAR_RULES = [
                    *(
                            var_objs or []
                    )
                ]

                context: str | None = self.getContext(table, self.OPERATION)

                if context:
                    self.CONTEXT = context.format(
                        TABLE_NAME=self.TABLE_NAME,
                        VAR_RULES=", ".join(map(
                            lambda x: self.ignorePrimaryKey(x).to_str(),
                            var_objs or []
                        ))
                    )
            else:
                raise Exception("TABLE not created anymore!")
        else:
            raise Exception("Undefined TABLE!")

    def getContext(self, table: DBTable, op: int) -> str | None:

        match op:

            case (self.New):
                for var_obj in self.VAR_RULES:
                    if var_obj in table.TABLE_VARS:
                        raise Exception("VAR is already included!")
                return "ALTER TABLE `{TABLE_NAME}` ADD COLUMN ({VAR_RULES})"

            case (self.Del):
                if len(self.VAR_RULES) > 0:
                    for var_obj in self.VAR_RULES:
                        if not (var_obj in table.TABLE_VARS):
                            raise Exception("VAR not already included!")
                else:
                    raise Exception("TABLE not contains this VAR anymore!")
                return "ALTER TABLE IF EXISTS `{TABLE_NAME}` DROP COLUMN ({VAR_RULES})"

        return None

    @staticmethod
    def ignorePrimaryKey(var_obj: DBVar) -> DBVar:
        if not var_obj.VAR_PRIMARY_KEY:
            return var_obj

        params: typing.List[int, ...] = []
        # if var_obj.VAR_PRIMARY_KEY:
        #     params.append(DBEnum.DB_PRIMARY_KEY)
        if not var_obj.VAR_NULLABLE:
            params.append(DBEnum.DB_NOT_NULL)
        if var_obj.VAR_AUTO_INCREMENT:
            params.append(DBEnum.DB_AUTO_INCREMENT)
        return DBVar(var_obj.VAR_NAME, var_obj.VAR_STRUCTURE, *params)


class DBLink(object):
    DB_CONNECT: DBConnect
    DB_PROGRAMS: typing.List[DBProgram]

    def __init__(self, cnx: DBConnect, *programs: DBProgram):
        super().__init__()
        self.DB_CONNECT = cnx
        self.DB_PROGRAMS = [*programs]

    def exec(self):
        return [
            self.DB_CONNECT.fetch(
                program.to_str(),
                getattr(program, "PARAMS", None)
            )
            for program in self.DB_PROGRAMS
        ]
        # rows: typing.List[typing.Any] = []
        # for program in self.DB_PROGRAMS:
        #
        #     params = getattr(program, "PARAMS", None)
        #     if params:
        #         rows.append(
        #             self.DB_CONNECT.fetch(
        #                 program.to_str(),
        #                 params
        #             )
        #         )
        #     else:
        #         rows.append(
        #             self.DB_CONNECT.fetch(
        #                 program.to_str()
        #             )
        #         )
        # return rows


class DBShowTables(object):
    DB_CONNECT: DBConnect

    def __init__(self, cnx: DBConnect):
        super().__init__()
        self.DB_CONNECT = cnx

    def to_list(self):

        # MySQL
        if not self.DB_CONNECT.DB_SQLITE:

            data: typing.List[typing.Any, ...] = self.DB_CONNECT.fetch(
                """
                SELECT TABLE_NAME
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA='{DB_NAME}'
                """.format(
                    DB_NAME=self.DB_CONNECT.DB_NAME
                )
            )

            return [
                *map(
                    lambda x: [
                        d for d in x
                    ],
                    data or []
                )
            ]

        else:

            data: typing.List[sqlite3.Row, ...] = self.DB_CONNECT.fetch(
                """
                SELECT NAME
                FROM sqlite_schema
                WHERE TYPE = 'table'
                AND name NOT LIKE 'sqlite_%'
                """
            )

            # normalize listed
            return [
                *map(
                    lambda x: [
                        d for d in x
                    ],
                    data or []
                )
            ]


class DBValueTypeStruct(DBObject):
    VAR_STRUCTURE: DBVarTypeStruct

    def __init__(self, var_structure: DBVarTypeStruct):
        super().__init__()
        self.VAR_STRUCTURE = var_structure

    def defineTypeVar(self) -> type:

        if type(self.VAR_STRUCTURE.VAR_TYPE) is int:
            match self.VAR_STRUCTURE.VAR_TYPE:
                case (DBVarTypeEnum.INT):
                    return int
                case (DBVarTypeEnum.REAL):
                    return float
                case (DBVarTypeEnum.CHAR |
                      DBVarTypeEnum.VARCHAR |
                      DBVarTypeEnum.TEXT):
                    return str
                # case (DBVarTypeEnum.CHAR |
                #       DBVarTypeEnum.VARCHAR |
                #       DBVarTypeEnum.TEXT |
                #       DBVarTypeEnum.TINYTEXT |
                #       DBVarTypeEnum.MEDIUMTEXT |
                #       DBVarTypeEnum.LONGTEXT):
                #     return str
                case (DBVarTypeEnum.BLOB):
                    return bytearray
                # case (DBVarTypeEnum.BLOB |
                #       DBVarTypeEnum.TINYBLOB |
                #       DBVarTypeEnum.MEDIUMBLOB |
                #       DBVarTypeEnum.LONGBLOB):
                #     return bytearray
                # case (DBVarTypeEnum.BINARY |
                #       DBVarTypeEnum.VARBINARY):
                #     return bytes
                case (_):
                    return str

        return str


class DBValue(DBValueTypeStruct):
    VAR_NAME: str
    VAR_VALUE: typing.Any

    def __init__(self, var_obj: DBVar, value: typing.Any):
        super().__init__(var_obj.VAR_STRUCTURE)
        self.VAR_NAME = var_obj.VAR_NAME
        if not var_obj.VAR_NULLABLE:
            if isinstance(value, types.NoneType):
                raise Exception("VAR not Nullable!")
            else:
                self.init(value)
        else:
            if isinstance(value, types.NoneType):
                self.VAR_NAME = var_obj.VAR_NAME
                self.VAR_VALUE = None
            else:
                self.init(value)

    def init(self, value: typing.Any):
        if type(value) is self.defineTypeVar():
            if self.VAR_STRUCTURE.VAR_TYPE in (DBVarTypeEnum.CHAR,):
                if len(value) > 1:
                    raise Exception("CHAR contains only 8 bit values!")
            self.VAR_VALUE = value
            self.CONTEXT = "{VAR_NAME} = '{VAR_VALUE}'".format(
                VAR_NAME=self.VAR_NAME,
                VAR_VALUE=self.VAR_VALUE
            )
        else:
            raise Exception("Wrong type for Value!")


class DBInsertTable(DBProgram):
    DB_TABLE: DBTable
    DB_VALUES: typing.List[DBValue]
    PARAMS: typing.List[typing.Any]
    # Typing typing.List[DBValue, ...]
    IGNORE_CHECKER: bool = False

    def __init__(self, table: DBTable, *value: DBValue, **kwargs):
        super().__init__()
        self.__dict__.update(kwargs)
        self.DB_TABLE = table
        self.DB_VALUES = [*value]
        if table:
            if table.TABLE_CREATED:

                if not self.IGNORE_CHECKER:
                    self.checker()

                self.CONTEXT = "INSERT INTO `{TABLE_NAME}`{VAR_RULES} VALUES {VAR_VALUES}".format(
                    TABLE_NAME=table.TABLE_NAME,
                    VAR_RULES=self.getRules(),
                    VAR_VALUES=self.getValues()
                )
                self.PARAMS = [
                    *map(
                        lambda x: getattr(x, "VAR_VALUE", None),
                        self.DB_VALUES or []
                        # if sorted rules, params must be sorted too
                    )
                ]
            else:
                raise Exception("TABLE not created anymore!")
        else:
            raise Exception("Undefined TABLE!")

    def checker(self):

        for var_obj in self.DB_TABLE.TABLE_VARS:
            included: bool = False
            for val_obj in self.DB_VALUES:
                if val_obj.VAR_NAME in (var_obj.VAR_NAME,):
                    included = True
            if not included:
                raise Exception("TABLE not contains this VAR anymore!")

    def getRules(self):

        i: int = 0
        n: int = len(self.DB_VALUES)
        context: str = "( "
        # # sorted like TABLE_VARS
        # # but conflict with params, and make slow
        # for var_obj in self.DB_TABLE.TABLE_VARS:
        #     for val_obj in self.DB_VALUES:
        #         if val_obj.VAR_NAME in (var_obj.VAR_NAME,):
        #             # DBValueTypeStruct(val_obj.VAR_STRUCTURE).defineTypeVar()
        #             if not n <= i + 1:
        #                 context += "`{VAR_NAME}`, ".format(
        #                     VAR_NAME=val_obj.VAR_NAME
        #                 )
        #             else:
        #                 context += "`{VAR_NAME}`".format(
        #                     VAR_NAME=val_obj.VAR_NAME
        #                 )
        #             i += 1
        for val_obj in self.DB_VALUES:
            if not n <= i + 1:
                context += "`{VAR_NAME}`, ".format(
                    VAR_NAME=val_obj.VAR_NAME
                )
            else:
                context += "`{VAR_NAME}`".format(
                    VAR_NAME=val_obj.VAR_NAME
                )
            i += 1
        context += " )"
        return context

    def getValues(self):

        i: int = 0
        n: int = len(self.DB_VALUES)
        context: str = "( "
        for _ in self.DB_VALUES:
            if not (n <= i + 1):
                context += "?, "
            else:
                context += "?"
            i += 1
        context += " )"
        return context


class DBUpdateTable(DBProgram):
    WHERES: typing.List[DBValue] | typing.Tuple[DBValue]

    """
        WHERE (columnN) LIKE (valueN)
    """

    def __init__(self, table: DBTable, *values: DBValue, **kwargs):
        super().__init__()
        self.__dict__.update(kwargs)


class DBDeleteFromTable(DBProgram):
    WHERES: typing.List[DBValue] | typing.Tuple[DBValue]


    def __init__(self, table: DBTable, *values: DBValue, **kwargs):
        super().__init__()
        self.__dict__.update(kwargs)


class DBDeleteTable(DBProgram): pass


class DBFindFromTable(DBProgram): pass


class DBRaw(object):
    pass


class DB(object):
    # CERTAIN
    AUTO_RESIZE: int = DBEnum.DB_AUTO_RESIZE

    # UNNECESSARY
    IF_EXISTS: int = DBEnum.DB_IF_EXISTS

    # STANDARD
    NOT_NULL: int = DBEnum.DB_NOT_NULL
    PRIMARY_KEY: int = DBEnum.DB_PRIMARY_KEY
    AUTO_INCREMENT: int = DBEnum.DB_AUTO_INCREMENT

    # CLASSES
    VarType: DBVarType = DBVarType
    Var: DBVar = DBVar
    Table: DBTable = DBTable
    DropTable: DBDropTable = DBDropTable
    CreateTable: DBCreateTable = DBCreateTable
    MoveTable: DBMoveTable = DBMoveTable
    ColumnTable: DBColumnTable = DBColumnTable
    Connect: DBConnect = DBConnect

    def __init__(self):
        pass


DBTextTypeNotNull = (
    DBVarType.TEXT(DBEnum.DB_AUTO_RESIZE),
    DBEnum.DB_NOT_NULL
)

if str(__name__).upper() in ("__MAIN__",):
    db: DBConnect = DBConnect(
        DB_HOST="localhost",
        DB_USER="coconuts",
        DB_PASSWORD="coconuts",
        DB_NAME="coconuts"
    )

    db.connect()

    db.fetch("ALTER TABLE IF EXISTS `guest` RENAME TO `user`")

    db.fetch("""DROP TABLE IF EXISTS `user`;""")
    db.fetch("""CREATE TABLE `user`(
        id VARCHAR(64) NOT NULL,
        user_photo_profile TEXT,
        user_first_name TEXT NOT NULL,
        user_last_name TEXT NOT NULL,
        user_unique_name TEXT NOT NULL,
        user_description TEXT,
        user_age TEXT NOT NULL,
        user_gender TEXT NOT NULL,
        user_email TEXT NOT NULL,
        user_password TEXT NOT NULL,
        user_phone_number TEXT,
        user_home_address TEXT,
        user_pos_code TEXT,
        time_zone TEXT NOT NULL,
        time TEXT NOT NULL,
        PRIMARY KEY (id)
    );""")

    # sqlite3
    # print(db.fetch("""SELECT name FROM sqlite_schema WHERE type ='table' AND name NOT LIKE 'sqlite_%';"""))

    # mysql
    print(db.fetch("""
    SELECT
        table_name
    FROM
        information_schema.TABLES
    WHERE
        TABLE_SCHEMA = 'coconuts';"""))

    # rename
    db.fetch("ALTER TABLE `user` RENAME TO `guest`")

    "ALTER TABLE `user` ADD COLUMN (declare)"

    "ALTER TABLE `user` DROP COLUMN `column_name`"

    "DROP TABLE IF EXISTS `user`"

    "DELETE FROM `user`"

    "DELETE FROM `user` WHERE id = ``"

    "INSERT INTO `user` (declare) VALUES (NULL, vars)"

    "UPDATE `user` SET user_age = WHERE id = ``"

    "SELECT id FROM `user` WHERE id = ``"

    '''
    
    DBObject dbObj = DB.CreateTable(
    
        DB.Var("id", BD.VarType.VARCHAR, DB.NOT_NULL, DB.PRIMARY_KEY)
    )
    '''

    drop_user: DBObject = DB.DropTable(DB.Table("coco"))

    print(drop_user.to_str())

    create_user: DBObject = DB.CreateTable(
        DB.Table("coco"),
        DB.Var(
            "id",
            DB.VarType.VARCHAR(64),
            DB.NOT_NULL,
            DB.PRIMARY_KEY
        ),
        DB.Var(
            "user_photo_profile",
            DB.VarType.TEXT(DB.AUTO_RESIZE)
        ),
        DB.Var(
            "user_first_name",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_last_name",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_unique_name",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_description",
            DB.VarType.TEXT(DB.AUTO_RESIZE)
        ),
        DB.Var(
            "user_age",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_gender",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_email",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_password",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_phone_number",
            DB.VarType.TEXT(DB.AUTO_RESIZE)
        ),
        DB.Var(
            "user_home_address",
            DB.VarType.TEXT(DB.AUTO_RESIZE)
        ),
        DB.Var(
            "user_country",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_city",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "user_pos_code",
            DB.VarType.TEXT(DB.AUTO_RESIZE)
        ),
        DB.Var(
            "time_zone",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        ),
        DB.Var(
            "time",
            DB.VarType.TEXT(DB.AUTO_RESIZE),
            DB.NOT_NULL
        )
    )

    print(create_user.to_str())

    # print(db.fetch(drop_user.to_str()))
    # print(db.fetch(create_user.to_str()))

    user_change_name: DBObject = DB.MoveTable(
        DBTable("coco"),
        DBTable("nuts")
    )

    print(user_change_name.to_str())

    user_add_column: DBObject = DBColumnTable(
        DBTable("coco"),
        DBColumnTable.New)
    #
    # print(db.fetch(user_change_name.to_str()))

    DB_USER: DBTable = DBTable("user")
    man: DBVar = DBVar("man", *DBTextTypeNotNull)

    print(
        DBColumnTable(DB_USER, DBColumnTable.Del, man).to_str()
    )

    print(db.fetch("""SHOW TABLES;"""))

    db.close()
