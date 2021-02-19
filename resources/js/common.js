import Axios from "axios"

export default {
    data(){
        return{

        }
    },
    methods:{
         async callApi(method, url, dataObj){
            try {
               return  await Axios({
                    method:method,
                    url:url,
                    data:dataObj

                })
            } catch (error) {
                return error.response
            }
        },
        info (desc, title = "Hey") {
            this.$Notice.info({
                title: title,
                desc: desc
            });
        },
        success (desc, title = "Great") {
            this.$Notice.success({
                title: title,
                desc: desc
            });
        },
        warning (desc, title = "Oops") {
            this.$Notice.warning({
                title: title,
                desc: desc
            });
        },
        error (desc, title = "Hey") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        },
        somethingWentWrong (desc = "Something Went Wrong!", title = "Oops") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        }
    }
}
