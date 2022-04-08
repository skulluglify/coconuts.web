import EyeVisible from "../Login/EyeVisible.mjs";
import CheckBox from "./CheckBox.mjs";
import DateOption from "./DateOption.mjs";
import PopOver from "./PopOver.mjs";
import PopUp from "./PopUp.mjs";

export default class Activity {

    static jessieQuery // JessieQuery Module Bindings Allocated Memory
    static DateOption // DateOption Module Bindings Allocated Memory
    static PopOver // PopOver Module Bindings Allocated Memory
    static PopUp // PopUp Module Bindings Allocated Memory

    static Main() {

        // Embedded Module Into Self Class
        this.jessieQuery.Module.Extends(

            EyeVisible,
            CheckBox,
            DateOption,
            PopOver,
            PopUp
        )

        let dateHasInitial = false

        let emailValidator = new RegExp(/^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i)

        let userName = document.querySelector("input#userName")
        let firstName = document.querySelector("input#firstName")
        let lastName = document.querySelector("input#lastName")
        let userPassword = document.querySelector("input#userPassword")
        let userConfirmPassword = document.querySelector("input#userConfirmPassword")
        let genderMale = document.querySelector("input#genderMale")
        let genderFemale = document.querySelector("input#genderFemale")
        let userEmail = document.querySelector("input#userEmail")
        let userPhoneNum = document.querySelector("input#userPhoneNum")
        let userAddress = document.querySelector("textarea#userAddress")
        let userDescription = document.querySelector("textarea#userDescription")

        if (userName &&
            firstName &&
            lastName &&
            userPassword &&
            userConfirmPassword &&
            genderMale &&
            genderFemale &&
            userEmail &&
            userPhoneNum &&
            userAddress &&
            userDescription) {

            let keyElements = {
                "user_photo": null,
                "user_name": null,
                "user_uniq": userName,
                "user_dob": null,
                "user_gender": null,
                "user_email": userEmail,
                "user_phone": userPhoneNum,
                "user_location": userAddress,
                "user_description": userDescription
            }

            for (let key in keyElements) {

                let el = keyElements[key]

                if (el) {

                    let context = localStorage.getItem(key)
                    if (context && context.length > 0) el.value = context
                } else if (key === "user_name") {

                    let context = localStorage.getItem(key)
                    if (context && context.length > 0) {

                        let contexts = context.split(",")

                        if (contexts.length > 1) {

                            lastName.value = contexts.pop()
                            firstName.value = contexts

                        } else {

                            firstName.value = contexts.shift()
                        }
                    }
                } else if (key === "user_dob") {

                    let context = localStorage.getItem(key)

                    if (context && context.length > 0) {

                        let date = new Date(context)
                        this.DateOption.Init(date)
                        dateHasInitial = true
                    }

                } else if (key === "user_gender") {

                    let context = localStorage.getItem(key)

                    if (context && context.length > 0) {

                        genderMale.checked = context === "male" && context !== "female"
                        genderFemale.checked = !genderMale.checked
                    }
                }
            }

            if (!dateHasInitial) this.DateOption.Init()

            document.querySelector("button#registry")
                .addEventListener("click", (async function () {

                    let date = new Date()
                    let userDateValue = this.DateOption.getDateValue()
                    let userDate = new Date(userDateValue)
                    let userAge = date.getFullYear() - userDate.getFullYear()

                    // Make Precisely
                    if (userDate.getMonth() > date.getMonth()) userAge = userAge - 1
                    else if (userDate.getDate() > date.getDate()) userAge = userAge - 1

                    let gender = genderMale.checked && !genderFemale.checked ? "male" : "female"

                    let attach = true
                    while (attach) {

                        if (userName.value.length === 0) {

                            await this.Alert("dangerous", "Username must be filled!")
                            break
                        }

                        if (firstName.value.length === 0) {

                            await this.Alert("dangerous", "First name must be filled!")
                            break
                        }

                        if (lastName.value.length === 0) {

                            await this.Alert("dangerous", "Last name must be filled!")
                            break
                        }

                        if (userPassword.value.length === 0) {

                            await this.Alert("dangerous", "Password must be filled!")
                            break
                        }

                        if (userPassword.value !== userConfirmPassword.value) {

                            await this.Alert("dangerous", "Password not same as confirm!")
                            break
                        }

                        if (userAge < 18) {

                            await this.Alert("dangerous", "Restrict age below 18 years!")
                            break
                        }

                        if (userEmail.value.length === 0) {

                            await this.Alert("dangerous", "Email must be filled!")
                            break
                        }

                        if (!emailValidator.test(userEmail.value)) {

                            await this.Alert("dangerous", "Email can't validate!")
                            break
                        }

                        // User Photo
                        // Phone Number
                        // Address
                        // Description
                        // Can Disable (Empty)

                        let data = {

                            "user_photo": null, // not handling it, coming soon
                            "user_name": [firstName.value.trim(), lastName.value.trim()].join(","),
                            "user_uniq": userName.value.trim(),
                            "user_dob": userDateValue,
                            "user_gender": gender,
                            "user_email": userEmail.value.trim(),
                            "user_pass": userPassword.value, // no trim
                            "user_phone": userPhoneNum.value.trim() || null,
                            "user_location": userAddress.value.trim() || null,
                            "user_description": userDescription.value.trim() || null
                        }

                        let response = await this.send(data)

                        if (response.status === 200 && response.statusText === "OK") {

                            let results = await response.json()

                            if ("error" in results) {

                                if ("message" in results.error) {

                                    switch (results.error.message) {

                                        case "var user_name cannot be empty!":

                                            await this.Alert("dangerous", "Real name must be filled!")
                                            break
                                        case "var user_uniq cannot be empty!":

                                            await this.Alert("dangerous", "User name must be filled!")
                                            break
                                        case "var user_dob cannot be empty!":

                                            await this.Alert("dangerous", "Date of birthday must be filled!")
                                            break
                                        case "var user_gender cannot be empty!":

                                            await this.Alert("dangerous", "User Gender must be filled!")
                                            break
                                        case "var user_email cannot be empty!":

                                            await this.Alert("dangerous", "User email must be filled!")
                                            break
                                        case "var user_pass cannot be empty!":

                                            await this.Alert("dangerous", "User password must be filled!")
                                            break
                                        case "var user_uniq already used by another user!":

                                            await this.Alert("dangerous", "User name already used by another user!")
                                            break
                                        case "var user_email already used by another user!":

                                            await this.Alert("dangerous", "User email already used by another user!")
                                            break
                                        case "failed insert table!":

                                            await this.Alert("dangerous", "Server problem!")
                                            break
                                        default:

                                            await this.Alert("dangerous", "Unknown reason! try next time :)")
                                            break
                                    }
                                }
                            } else
                            if ("success" in results) {

                                if ("message" in results.success) {

                                    switch (results.success.message) {

                                        case "success insert table!":

                                            let token = "token" in results ? results.token : "<unknown>"
                                            await this.Alert("success", "Congratulations you have registered!")
                                            await this.Alert("warning", "Your Token: " + token)
                                            break
                                        default:

                                            await this.Alert("warning", "Unknown reason! but you are already signed")
                                            break
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
    }

    /**
     * @param {string} info
     * @param {string} message
     * @param {string} type
     * @param {function} callback
     * */
    static async Alert(info, message, type= "submit") {

        return new Promise((function (resolve, reject) {

            try {

                this.PopUp.createPopUp({

                    info: info,
                    message: message,
                    type: type,
                    callback: resolve
                })

            } catch (e) {

                reject(e)
            }

        }).bind(this))
    }

    /**
     * @param {Object} data*/
    static async send(data) {

        if (!(typeof data == "object" && !Array.isArray(data))) return new Promise(function (resolve, reject) {

            reject(null)
        })

        return fetch(location.origin + "/public/v1/registry", {

            method: "POST",
            mode: "cors",
            cache: "no-cache",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json"
            },
            redirect: "follow",
            referrerPolicy: "origin",
            body: JSON.stringify({

                // Root (Rules in My php script)
                registry: data
            })
        })
    }
}