import Utilities from "../Registry/Utilities.js";

export default class Activity {

    static jessieQuery
    static Utilities

    static Main() {

        this.jessieQuery.Module.Extends(

            Utilities
        )

        let photo = document.querySelector("div.content div.user-photo")
        let description = document.querySelector("div.content div.user-description")

        let token = localStorage.getItem("token")

        if (token && typeof token == "string" && token.length > 0) {

            (async function() {

                let [ response ] = await this.Utilities.SendRequest({
                    identify: {
                        token: token
                    }
                })

                let data = await response.json()

                // no set successfully message
                if (!("error" in data)) {

                    let user_photo = "user_photo" in data ? data.user_photo : null

                    if (user_photo)
                        Object.assign(photo.style, {

                            backgroundImage: "url(\"" + location.origin + "/public/v1/image?src=" + user_photo + "\")"
                        })
                    else
                        await this.Utilities.Alert("warning", "User does not contain images yet!")

                    let table = document.createElement("table")
                    let tbody = document.createElement("tbody")

                    table.appendChild(tbody)
                    description.appendChild(table)

                    for (let key in data) {

                        let value = data[key]

                        if ("user_photo" === key) continue
                        if ("user_name" === key) value = value.replace(",", " ")

                        let tr = document.createElement("tr")
                        let tdKey = document.createElement("td")
                        let tdValue = document.createElement("td")
                        let divKey = document.createElement("div")
                        let divValue = document.createElement("div")

                        key = key.substring(5)

                        if (key === "uniq") key = "user name"
                        if (key === "name") key = "full name"
                        if (key === "dob") key = "date of birth"
                        if (key === "location") key = "address"

                        divKey.textContent = key
                        divValue.textContent = value

                        key = key.replace(/ /g, "-")

                        divKey.classList.add(key)
                        divValue.classList.add(key)

                        tdKey.appendChild(divKey)
                        tdValue.appendChild(divValue)
                        tr.append(tdKey, tdValue)

                        tbody.appendChild(tr)
                    }
                } else {

                    await this.Utilities.Alert("dangerous", "message" in data.error ? data.error.message : "Server problem!")
                    location.href = location.origin + "/public/index.html"
                }
            })
                .call(this)
                .then()

        } else {

            (async function() {

                await this.Utilities.Alert("dangerous", "You must login first!")
                location.href = location.origin + "/public/index.html"
            })
                .call(this)
                .then()
        }
    }

    static spanText(context) {

        if (context && typeof context == "string" && context.length > 0) {

            let span = document.createElement("span")
            span.textContent = context
            return span
        }

        return null
    }
}