<template>
    <div>
        <div v-if="isInstalled" class="card bg-white shadow-lg rounded">
            <div class="card-header">
                {{title}} <span class="float-right"><a :href="domainName" target="_blank">Visit Site</a></span>
            </div>
            <div class="card-body">
                <p v-html="message"></p>
            </div>
        </div>
        <div class="card bg-white shadow-lg rounded" v-show="loading===true">
            <div class="card-header" id="waitTitle">
                Please wait.
            </div>
            <div class="card-body">
                <h2 id="waitMessage">Saving Info...</h2>
            </div>
        </div>
        <div class="card bg-white shadow-lg rounded" v-if="!loading && isInstalled===false">
            <div class="card-header">
                {{getTitle()}}
            </div>
            <div class="card-body p-4">
                <form method="post">
                    <div v-show="currentStep===1">
                        <div class="form-group row mb-3">
                            <label for="site_title">Site Title</label>
                            <input id="site_title" name="site_title" type="text" class="form-control" v-model="form.site_title" required />
                            <div class="text text-danger">{{this.errors.site_title}}</div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="site_name">Site Name</label>
                            <input id="site_name" name="site_name" type="text" class="form-control"  v-model="form.site_name" required />
                            <div class="text text-danger">{{this.errors.site_name}}</div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="site_domain">Site Domain</label>
                            <input id="site_domain" name="site_domain" type="text" class="form-control"  v-model="form.site_domain" required />
                            <div class="text text-danger">{{this.errors.site_domain}}</div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="site_context">Site Context</label>
                            <input name="site_context" id="site_context"  type="text" class="form-control" v-model="form.site_context" required />
                            <small>If you change here. Please make sure to change in your .env file too.</small>
                            <div class="text text-danger">{{this.errors.site_context}}</div>
                        </div>
                    </div>
                    <div v-show="currentStep===2">
                        <div class="form-group row mb-3">
                            <label for="name">Full Name</label>
                            <input id="name" name="full_name" type="text" class="form-control"  v-model="form.name" required />
                            <div class="text text-danger">{{this.errors.name}}</div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="user_email">Email</label>
                            <input id="user_email" name="user_email" type="text" class="form-control"  v-model="form.user_email" required />
                            <div class="text text-danger">{{this.errors.user_email}}</div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="user_password">Password</label>
                            <input id="user_password" name="user_password" type="text" class="form-control"  v-model="form.user_password" required />
                            <div class="text text-danger">{{this.errors.user_password}}</div>
                        </div>
                    </div>

                    <div class="pull-right" style="text-align: center">
                        <button type="button" v-if="currentStep===1" class="btn btn-primary btn-lg" @click="goToNextStep()">Next</button>
                        <template v-if="currentStep===2">
                            <button type="button" class="btn btn-outline-secondary btn-lg" @click="goToPrevStep()">Previous</button>
                            &nbsp;<button type="button" class="btn btn-success btn-lg" @click="saveSite()">Save Info</button>
                        </template>

                        <span class="pull-right">&nbsp;{{currentStep}}/2</span>
                    </div>

                </form>
            </div>
        </div>

    </div>
</template>

<script>
    import Form from "../helpers/form";
    export default {
        props: [
          'dataSiteInfo',
          'dataIsInstalled'
        ],
        mounted() {
            //console.log('Component mounted.');
            this.init();
        },
        computed: {
          domainName() {
              let protocol = window.location.protocol;
              let port = window.location.port;
              let domain = (this.siteInfo.domain.indexOf("http") >= 0) ? this.siteInfo.domain : protocol+"//"+this.siteInfo.domain;
              domain = (domain+"/example").replace("//example", "/example");
              return domain;
          }
        },
        data() {
          return {
              currentStep:1,
              message:`Your site is configured.<br />
                    <code>Powered By <a target="_blank" href='https://www.hashtagcms.org/?utm_source=${window.location.href}'>HashtagCms</a></code>
                    `,
              siteInfo:(typeof this.dataSiteInfo === "undefined" || this.dataSiteInfo === "") ? [] : JSON.parse(this.dataSiteInfo),
              isInstalled:(typeof this.dataIsInstalled === "undefined" || this.dataIsInstalled === "") ? false : parseInt(this.dataIsInstalled),
              form: new Form({
                  site_name: "",
                  site_title:"",
                  site_context:"",
                  site_domain:"",
                  user_email:"",
                  user_password:"",
                  name:""
              }),
              errors:{},
              saveURL:"/install/save",
              loading:false,
              title:'Congratulations!'
          }
        },
        methods: {
            getTitle() {
                return this.currentStep === 1 ? "Site Info" : "User Info";
            },
            showLoader(show) {
                this.loading = show;
            },
            goToPrevStep() {
                this.currentStep = this.currentStep - 1;
            },
            goToNextStep() {
              this.currentStep = this.currentStep + 1;
            },
            init() {
                this.form.site_name = this.siteInfo.name;
                this.form.site_title = this.siteInfo.lang.title;
                this.form.site_context = this.siteInfo.context;
                this.form.site_domain = this.siteInfo.domain;
            },
            showError(res) {
                this.showLoader(false);
                //console.log("res");
                //console.log(res);
                for(let i in res.errors) {
                    if(res.errors.hasOwnProperty(i)) {
                        this.errors[i] = res.errors[i][0];
                        if(i.indexOf("site_")>=0) {
                            this.currentStep = 1;
                        }
                    }
                }

                this.errorMessage = res.message;

            },
            hideErrorMessage(event) {

                let name = event.target.getAttribute("name");
                this.errors[name] = "";

                if(this.errorMessage !== '') {
                    this.errorMessage = ''
                }
            },
            saveSite() {
                let $this = this;
                this.showLoader(true);

                this.form.submit("post", this.saveURL)
                    .then(function (response) {
                        if(response['error']) {
                            $this.showLoader(false);
                            $this.message = response.error;
                            $this.title = response.title;
                            $this.isInstalled = true;
                        } else {
                            $this.isInstalled = response.isInstalled;
                            $this.siteInfo = {};
                            $this.siteInfo = response.siteInfo;
                            $this.showLoader(false);
                        }
                    })
                    .catch(response => this.showError(response));

            }
        }
    }
</script>
