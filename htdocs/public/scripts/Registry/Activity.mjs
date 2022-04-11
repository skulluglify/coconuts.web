import EyeVisible from "../Login/EyeVisible.mjs";
import DateOption from "./DateOption.mjs";
import ImageUploader from "./ImageUploader.mjs";
import CheckBox from "./CheckBox.mjs";
import PopOver from "./PopOver.mjs";
import Utilities from "./Utilities.mjs";

export default class Activity {

    static jessieQuery // JessieQuery Module Bindings Allocated Memory
    static DateOption // DateOption Module Bindings Allocated Memory
    static Utilities // DateOption Module Bindings Allocated Memory
    static ImageUploader // ImageUploader Module Bindings Allocated Memory
    // static CheckBox // ImageUploader Module Bindings Allocated Memory
    static PopOver // PopOver Module Bindings Allocated Memory
    static userPhoto // make new Allocated Memory

    static Main() {

        // Embedded Module Into Self Class
        this.jessieQuery.Module.Extends(

            EyeVisible,
            Utilities,
            DateOption,
            ImageUploader,
            CheckBox,
            PopOver
        )

        let dateHasInitial = false

        this.PopOver.setPopContent("Your Birthday!")

        let userName = document.querySelector("input#userName")
        let userPhoto = document.querySelector("div.user-photo")
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
            userPhoto &&
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

                let context = localStorage.getItem(key)

                if (context && typeof context == "string" && context.length > 0) {

                    // Auto fill Cache
                    if (el) el.value = context
                    else if (key === "user_photo") {

                        if (context.startsWith("data:image\/")) {
                            Object.assign(userPhoto.style, {

                                backgroundImage: "url(\"" + context + "\")"
                            })
                            this.userPhoto = this.Utilities.convertDataURLToBlob(context)
                        }
                    }
                    else if (key === "user_name") {

                        let contexts = context.split(",")

                        if (contexts.length > 1) {

                            lastName.value = contexts.pop()
                            firstName.value = contexts

                        } else {

                            firstName.value = contexts.shift()
                        }

                    } else if (key === "user_dob") {

                        let date = new Date(context)
                        this.DateOption.Init(date)
                        dateHasInitial = true

                    } else if (key === "user_gender") {


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

                            await this.Utilities.Alert("dangerous", "User name must be filled!")
                            break
                        }

                        if (firstName.value.length === 0) {

                            await this.Utilities.Alert("dangerous", "First name must be filled!")
                            break
                        }

                        if (lastName.value.length === 0) {

                            await this.Utilities.Alert("dangerous", "Last name must be filled!")
                            break
                        }

                        if (userPassword.value.length === 0) {

                            await this.Utilities.Alert("dangerous", "Password must be filled!")
                            break
                        }

                        if (userPassword.value !== userConfirmPassword.value) {

                            await this.Utilities.Alert("dangerous", "Password not same as confirm!")
                            break
                        }

                        if (userAge < 18) {

                            await this.Utilities.Alert("dangerous", "Restrict age below 18 years!")
                            break
                        }

                        if (userEmail.value.length === 0) {

                            await this.Utilities.Alert("dangerous", "Email must be filled!")
                            break
                        }

                        if (!this.Utilities.isEmail(userEmail.value)) {

                            await this.Utilities.Alert("dangerous", "Email can't validate!")
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

                        let [ response ] = await this.Utilities.SendRequest({

                            // Rules [In My Php Code]
                            registry: data
                        })

                        if (response) {

                            if (response.status === 200 && response.statusText === "OK") {

                                let results = await response.json()

                                // var user_name cannot be empty!
                                // var user_uniq cannot be empty!
                                // var user_dob cannot be empty!
                                // var user_gender cannot be empty!
                                // var user_email cannot be empty!
                                // var user_pass cannot be empty!
                                // var user_uniq already used by another user!
                                // var user_email already used by another user!
                                // success insert table!
                                // failed insert table!

                                if ("error" in results) {

                                    if ("message" in results.error) {

                                        switch (results.error.message) {

                                            case "var user_name cannot be empty!":

                                                await this.Utilities.Alert("dangerous", "Real name must be filled!")
                                                break
                                            case "var user_uniq cannot be empty!":

                                                await this.Utilities.Alert("dangerous", "User name must be filled!")
                                                break
                                            case "var user_dob cannot be empty!":

                                                await this.Utilities.Alert("dangerous", "Date of birthday must be filled!")
                                                break
                                            case "var user_gender cannot be empty!":

                                                await this.Utilities.Alert("dangerous", "User Gender must be filled!")
                                                break
                                            case "var user_email cannot be empty!":

                                                await this.Utilities.Alert("dangerous", "User email must be filled!")
                                                break
                                            case "var user_pass cannot be empty!":

                                                await this.Utilities.Alert("dangerous", "User password must be filled!")
                                                break
                                            case "var user_uniq already used by another user!":

                                                await this.Utilities.Alert("dangerous", "User name already used by another user!")
                                                break
                                            case "var user_email already used by another user!":

                                                await this.Utilities.Alert("dangerous", "User email already used by another user!")
                                                break
                                            case "failed insert table!":

                                                await this.Utilities.Alert("dangerous", "Server problem!")
                                                break
                                            default:

                                                await this.Utilities.Alert("dangerous", "Unknown reason! try next time :)")
                                                break
                                        }
                                    }
                                } else
                                if ("success" in results) {

                                    if ("message" in results.success) {

                                        switch (results.success.message) {

                                            case "success insert table!":

                                                let token = "token" in results ? results.token : "<unknown/>"
                                                await this.Utilities.Alert("success", "Congratulations you have registered!")
                                                await this.Utilities.Alert("warning", "Your Token: " + token)

                                                // Send User Photo
                                                let file = this.userPhoto

                                                if (file && Blob.prototype.isPrototypeOf(file)) {

                                                    let formData = new FormData
                                                    formData.append("user_photo", file)
                                                    formData.append("user_token", token)
                                                    let [ response ] = await this.Utilities.SendRequest({
                                                        upload: formData
                                                    })

                                                    if (response) {
                                                        let unsafe = await response.text()
                                                        console.log(unsafe)
                                                    }
                                                }

                                                break
                                            default:

                                                await this.Utilities.Alert("warning", "Unknown reason! but you are already signed")
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

            let imgUploader = document.querySelector("#Img_Uploader")

            if (imgUploader) {

                let imgUploaderClone = this.Utilities.cloneElement(imgUploader)
                imgUploader.remove()

                userPhoto.addEventListener("click", (async function () {

                    await this.ImageUploader.show()

                    let dataURL = this.ImageUploader.getAsDataURL()
                    let file = this.ImageUploader.getAsFile()

                    if (file) this.userPhoto = file

                    if (dataURL) {

                        Object.assign(userPhoto.style, {

                            backgroundImage: "url(\"" + dataURL + "\")"
                        })

                        // store cache
                        localStorage.setItem("user_photo", dataURL)
                    }

                }).bind(this))

                this.ImageUploader.Init(imgUploaderClone)
            }
        }
    }
}