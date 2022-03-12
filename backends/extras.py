#!/usr/bin/env python3

def c_is_alphabets(c: str) -> bool:
    x: int
    x = ord(c)

    if 65 <= x <= 90 or 97 <= x <= 122:
        return True

    return False


def c_is_nums(c: str) -> bool:
    x: int
    x = ord(c)

    if 48 <= x <= 57:
        return True

    return False


def auto_int(context: str) -> int | float | str:
    c: chr
    n: int
    dots: int
    is_num: bool
    temp: str

    n = len(context)
    dots = 0
    is_num = True
    temp = ""

    for i in range(n - 1):

        c = context[i]

        if c_is_nums(c):

            temp += c
            continue

        elif c == ".":

            if dots == 1:
                is_num = False
                break

            dots += 1
            temp += c

        else:

            is_num = False
            break

    if is_num:

        c = context[n - 1]

        if dots == 0:

            if c == "f":
                return float(temp)

            if c_is_nums(c):
                return int(
                    context
                )

        elif dots == 1:

            c = "0" if c == "f" else c

            return float(temp if not c_is_nums(c) else temp + c)

    return 0


if str(__name__).upper() in ("__MAIN__",):
    print(auto_int("123"))
    print(auto_int("123f"))
    print(auto_int("123.33"))
    print(auto_int("123.33f"))
    print(auto_int("123.000.000"))
    print(auto_int("E123"))
    print(auto_int("123E"))
    print(auto_int("Q123E"))
    print(auto_int("Q12A3E"))
