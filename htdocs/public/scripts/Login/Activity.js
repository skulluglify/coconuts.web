import EyeVisible from "./EyeVisible.js";
import Utilities from "../Registry/Utilities.js";

export default class Activity {

    static jessieQuery // JessieQuery Module Bindings Allocated Memory
    static Utilities // Utilities Module Bindings Allocated Memory

    static Main() {

        // Embedded Module Into Self Class
        this.jessieQuery.Module.Extends(

            EyeVisible,
            Utilities
        )

        let userNameOrEmail = document.querySelector("div.input input#userNameOrEmail")
        let userPassword = document.querySelector("div.input input#userPassword")

        // Login
        let loginBtn = document.querySelector("div.submit button#login")
        if (userNameOrEmail &&
            userPassword &&
            loginBtn) {

            let context = localStorage.getItem("user_uniq")
            if (context && typeof context == "string" && context.length > 0) {

                userNameOrEmail.value = context

            }

            loginBtn.addEventListener("click", (async function(e) {

                let target = e && "target" in e && e.target

                let attach = true
                while (attach) {

                    if (userNameOrEmail.value.length === 0) {

                        await this.Utilities.Alert("dangerous", "User name or email must be filled!")
                        break
                    }

                    if (userPassword.value.length === 0) {

                        await this.Utilities.Alert("dangerous", "User password must be filled!")
                        break
                    }

                    let data = {

                        "user_uniq": null,
                        "user_email": null,
                        "user_pass": userPassword.value.trim()
                    }

                    let context = userNameOrEmail.value
                    if (this.Utilities.isEmail(context)) data["user_email"] = context
                    else data["user_uniq"] = context.trim()

                    let [ response ] = await this.Utilities.SendRequest({

                        // Rules [In My Php Code]
                        login: data
                    })

                    if (response) {

                        if (response.status === 200 && response.statusText === "OK") {

                            let results = await response.json()

                            // var user_uniq, user_email not found!
                            // var user_pass cannot be empty!
                            // You are blocked, due to abnormal traffic activity!
                            // Something went wrong on the server!
                            // You are blocked, for exceeding the login limit!
                            // success login
                            // failed login!

                            if ("error" in results) {

                                if ("message" in results.error) {

                                    switch (results.error.message) {

                                        case "var user_uniq, user_email not found!":

                                            await this.Utilities.Alert("dangerous", "User name or email not found!")
                                            break
                                        case "var user_pass cannot be empty!":

                                            await this.Utilities.Alert("dangerous", "User password must be filled!")
                                            break
                                        case "You are blocked, due to abnormal traffic activity!":

                                            await this.Utilities.Alert("dangerous", "You got banned!")
                                            break
                                        case "Something went wrong on the server!":

                                            await this.Utilities.Alert("dangerous", "Server problem!")
                                            break
                                        case "You are blocked, for exceeding the login limit!":

                                            await this.Utilities.Alert("dangerous", "You are blocked!")
                                            break
                                        case "failed login!":

                                            await this.Utilities.Alert("dangerous", "Failed Login!")
                                            break
                                        default:

                                            await this.Utilities.Alert("dangerous", "Unknown reason! try next time :)")
                                            break
                                    }
                                }
                            }
                            if ("success" in results) {

                                if ("message" in results.success) {

                                    switch (results.success.message) {

                                        case "success login!":

                                            let token = "token" in results ? results.token : "<unknown/>"
                                            await this.Utilities.Alert("success", "Congratulations you have signed!")

                                            // saved token
                                            localStorage.setItem("token", token)

                                            await this.Utilities.Alert("warning", "Your Token: " + token)
                                            location.href = location.origin + "/public/identify.html"

                                            break
                                        default:

                                            await this.Utilities.Alert("warning", "Unknown reason! but you are already login")
                                            break
                                    }
                                }
                            }
                        }
                    }

                    // save cache
                    for (let key in data) {

                        let context = data[key]
                        if (context && key !== "user_pass") localStorage.setItem(key, context)
                    }

                    attach = false
                }

            }).bind(this))
        }

        // Registry
        let regisBtn = document.querySelector("div.submit button#registry")
        if (regisBtn) {
            /**
             * @param {Event} e
             */
            regisBtn.addEventListener("click", function (e) {

                // open("registry.html")
                location.href = location.origin + "/public/registry.html"
            })

        }
    }
}