<template>
    <div class="container">
        
        <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users Table</h3>
               
                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal" >
                         Add New
                        <i class="fas fa-user-plus fa-fw"></i>
                        </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>Type</th>                   
                    <th>Registered At</th>
                     <th>Modify</th>
                  </tr>
                  <tr v-for="user in users" :key ="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.type | textToUpper }}</td>
                    <td>{{ user.created_at | myDate }}</td>
                    <td>
                        <a href="#" @click="editModal(user)">                            
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="#" v-on:click.prevent="deleteUser(user.id)">                            
                            <i class="fa fa-trash text-red" ></i>
                        </a>
                    </td>
                  </tr>                
                  
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- MODAL -->
            <div id="addNew" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewLabel" v-show="!editMode">Add New</h5>
                        <h5 class="modal-title" id="addNewLabel" v-show="editMode">Update User's Info</h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <form @submit.prevent=" editMode ? updateUser() : createUser()">  
                      <div class="modal-body">
                        <div class="form-group">                      
                          <input v-model="form.name" id="name" type="text" name="name" placeholder="Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                          <has-error :form="form" field="name"></has-error>
                        </div>

                        <div class="form-group">                      
                          <input v-model="form.email" id="email" type="email" name="email" placeholder="Email Adress"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                          <has-error :form="form" field="email"></has-error>
                        </div>                   

                        <div class="form-group">                      
                          <textarea v-model="form.bio" id="bio" type="text" name="bio" placeholder="short bio for user"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }" ></textarea>
                          <has-error :form="form" field="bio"></has-error>
                        </div>

                        <div class="form-group">                      
                          <select v-model="form.type"  id="type" name="type" placeholder="Email Adress"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                            <option value="">select user role</option>
                            <option value="admin">Admin</option>
                            <option value="user">Standard used</option>
                            <option value="author">Author</option>                          
                          </select>
                          <has-error :form="form" field="username"></has-error>
                        </div>

                        <div class="form-group">                      
                          <input v-model="form.password" type="password" name="password" placeholder="Enter Password"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                          <has-error :form="form" field="password"></has-error>
                        </div>

                      </div>
                      <!--modal footer-->
                      <div class="modal-footer">
                          <button class="btn btn-success" type="submit" v-show="editMode">update</button>
                           <button class="btn btn-primary" type="submit" v-show="!editMode">create</button>
                          <button  class="btn btn-danger" data-dismiss="modal">close</button>
                      </div>
                    </form>  
                </div> 
            </div> 
            </div>

        </div>
    </div>
</template>

<script>
import { setTimeout } from 'timers';
    export default {

        data() {
          return {
            editMode: false,
            users: {},
            form : new Form({
              id: '',
              name: '',
              email: '',
              password: '',
              type: '',
              bio: '',
              photo: ''
            })
          }
        },

        filters: {

        },

        methods: {
          loadUsers(){
           if(this.$gate.isAdminOrAuthor()){
              axios.get('api/user')
              .then(({data}) => (this.users = data.data));
           }
          },

          updateUser(user){  
                    
            this.form.put('api/user/' + this.form.id)
            .then(()=> {
              //successfull
              $('#addNew').modal('hide');
                Swal.fire(
                    'Updated!',
                    'Information has been updated.',
                    'success'
                  )
              eventBus.$emit('userCreated');               
            })
            .catch(()=> {
            
            })
          },

          editModal(user) {
            this.editMode = true; 
            this.form.reset(); 
             $('#addNew').modal('show');         
            this.form.fill(user);
           

          },

          newModal() {
             this.editMode = false;
            this.form.reset();
             $('#addNew').modal('show');
          },

         deleteUser(id){ 
                        
            Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
              //send ajax request to the server (using the vform )
              if (result.value) {
                this.form.delete('api/user/' + id).then(()=> {
                  Swal.fire(
                    'Deleted!',
                    'User has been deleted.',
                    'success'
                  )
                eventBus.$emit('userCreated')
                }).catch(()=> {
                  Swal.fire("Failed", "Something went wrong", "warning")
                })
              }
            })
              
          },

          createUser(){
           
            //sent request via post
            this.$Progress.start();

            this.form.post('api/user')
            .then(() => {
              eventBus.$emit('userCreated'); 
              $('#addNew').modal('hide');             
              Toast.fire({
                  type: 'success',
                  title: 'User created successfully'
                });
                
                this.$Progress.finish();
            })
            .catch(()=> {})  
           
          }
        },

        created() {
          this.loadUsers();
          eventBus.$on('userCreated', () => {
              this.loadUsers();
          });

          //setInterval(() => this.loadUsers(), 3000);          
        }
    }
</script>
