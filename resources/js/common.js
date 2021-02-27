import Axios from "axios"
import {mapGetters} from 'vuex'
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
        },
        handleSpinCustom () {
            this.$Spin.show({
                render: (h) => {
                    return h('div', [
                        h('Icon', {
                            'class': 'demo-spin-icon-load',
                            props: {
                                type: 'ios-loading',
                                size: 18
                            }
                        }),
                        h('div', 'Loading')
                    ])
                }
            });
            setTimeout(() => {
                this.$Spin.hide();
            }, 2000);
        },

        //yeha key read, write, update, delete huncha jun chai tala computed property bata pathaincha
        checkUserPermission(key){
            //yedi permission diyeko chaina vanae true return gardine i.e sabai permission paucha
            if(!this.userPermission) return true
            let isPermitted = false
            for(let data of this.userPermission){
                //data aayesi route ko name ra userPermisison object ma hune name ko value same cha vane i.e tags = tags cha vanae matrai persmission check garne
                if(this.$route.name==data.name){
                    if(data[key]){
                        isPermitted = true
                        break
                    }
                    break
                }
            }
            return isPermitted
        }
    },
    computed:{
        ...mapGetters({
            'userPermission':'getUserPermission'
        }),
        isReadPermitted(){
            return this.checkUserPermission('read')
        },
        isWritePermitted(){
            return this.checkUserPermission('write')
        },
        isUpdatePermitted(){
            return this.checkUserPermission('update')
        },
        isDeletePermitted(){
            return this.checkUserPermission('delete')

        },
    }
}

