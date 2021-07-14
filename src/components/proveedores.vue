<template>
  <v-card class="mx-auto" width="1200">
    <br />
    <v-label><h1 style="margin-left: 10px">Proveedores</h1></v-label>
    <v-text-field color="purple darken-4" loading disabled></v-text-field>
    <v-data-table
      :headers="headers"
      :items="desserts"
      sort-by="nombre"
      class="elevation-1"
      :search="search"
    >
      <template v-slot:top>
        <v-toolbar flat color="black">
          <v-toolbar-title>Proveedores</v-toolbar-title>

          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-text-field
            v-model="search"
            label="Buscar (POR NOMBRE)"
            class="mx-2"
          ></v-text-field>

          <v-spacer></v-spacer>
          <v-dialog v-model="dialogprov" max-width="500px">
            <v-card>
              <v-card-title>
                <span class="headline">Almacena detalle de Proveedores</span>
              </v-card-title>

              <v-card-text>
                <v-textarea
                  outlined
                  v-model="detalleproveedor"
                  label="Detalle"
                ></v-textarea>
                <v-btn outlined color="green" @click="guardadetalle()"
                  >Guardar</v-btn
                >
              </v-card-text>
            </v-card>
          </v-dialog>
           <v-dialog v-model="dialogdetalles" max-width="500px">
            <v-card>
              <v-card-title>
                <span class="headline">Detalle Proveedor</span>
              </v-card-title>

              <div style="margin-left: 50px">
              <v-label v-for="(art,index) in arraydetalle" :key="index">
                {{ art.detalle }} __________Fecha:  {{art.fecha}}
                <br>

              </v-label>
            </div>

              <v-card-text>
               <br>
 <v-btn color="red"   style="margin-left: 300px" outlined @click="cierra()"
                >Cerrar</v-btn
              >
                
              </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-2" v-on="on"
                >Nuevo Item</v-btn
              >
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.nombre"
                        label="Nombre"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.telefono"
                        label="Telefono"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.email"
                        label="Email"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.cuil"
                        label="Cuil"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close"
                  >Cancelar</v-btn
                >
                <v-btn color="blue darken-1" text @click="save">Guardar</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small class="mr-2" @click="editDetalle(item)">
          mdi-format-line-style
        </v-icon>
        <v-icon small @click="detallepov(item)"> mdi-search-web </v-icon>


        <v-icon color="red" small @click="deleteItem(item)"> mdi-delete </v-icon>

      </template>
      <template v-slot:no-data>
        <v-btn color="primary" @click="initialize">Reset</v-btn>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
export default {
  data: () => ({
    dialogdetalles: false,
    detalleproveedor: "",
    auxpkproveedor: 0,
    idd: "",
    dialogprov: false,
    nombree: "",
    descripcionn: "",
    dialog: false,
    search: "",
    headers: [
      {
        text: "Id",
        align: "start",
        sortable: false,
        value: "id",
      },

      { text: "Nombre", value: "nombre" },
      { text: "Telefono", value: "telefono" },
      { text: "Email", value: "email" },
      { text: "Cuit", value: "cuil" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    desserts: [],
    arraydetalle:[],
    auxdetalles:[],
    editedIndex: -1,
    editedItem: {
      id: "",
      nombre: "",
      telefono: "",
      email: "",
      cuil: "",
    },
    defaultItem: {
      id: "",
      nombre: "",
      telefono: "",
      email: "",
      cuil: "",
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Proveedor" : "Edita Proveedor";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
  },

  created() {
    this.initialize();
     this.traedetalles()
  },

  methods: {
    cierra(){
    this.dialogdetalles= false;
    this.arraydetalle=[]
    },
    initialize() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/proveedores.php",
        requestOptions
      )
        .then((response) => response.json())
        .then((result) => (this.desserts = result))
        .catch((error) => console.log("error", error));
    },

    editItem(item) {
      this.idd = item.id;
      this.nombree = item.nombre;
      this.descripcionn = item.descripcion;
      this.editedIndex = this.desserts.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    editDetalle(prov) {
      this.auxpkproveedor = prov.id;
      this.dialogprov = true;
    },
    detallepov(prov) {
         //  this.auxpkproveedor = prov.id;
      this.dialogdetalles = true;
      this.auxdetalles.forEach((element) => {
        if (element.fkproveedor== prov.id) {
          this.arraydetalle.push(element);
        }
      })
    //  console.log(this.auxdetalles)
    },
    traedetalles(){
       var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/detalleproveedores.php",
        requestOptions
      )
        .then((response) => response.json())
        .then((result) => (this.auxdetalles = result))
        .catch((error) => console.log("error", error));

    },
    guardadetalle() {
     

      var formdata = new FormData();
      formdata.append("id", "");
      formdata.append("detalle", this.detalleproveedor);
      formdata.append("fkproveedor", this.auxpkproveedor);
      formdata.append("fecha", new Date().toISOString().substr(0, 10));

      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/detalleproveedores.php",
          { method: "POST", body: formdata }
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        alert("Se almaceno con exito!!");
        console.log(data);
        this.dialogprov = false;
      });
    },

    deleteItem(item) {
      const index = this.desserts.indexOf(item);
      confirm("Esta seguro de eliminar este item?") &&
        this.desserts.splice(index, 1);

      var requestOptions = {
        method: "DELETE",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/proveedores.php?id=" +
          item.id,
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .catch((error) => console.log("error", error));
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      if (this.editedIndex > -1) {
        var requestOptions = {
          method: "PUT",

          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/proveedores.php?id=" +
            this.editedItem.id +
            "&nombre=" +
            this.editedItem.nombre +
            "&telefono=" +
            this.editedItem.telefono +
            "&email=" +
            this.editedItem.email +
            "&cuil=" +
            this.editedItem.cuil,
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result));
        this.initialize();
        //  Object.assign(this.desserts[this.editedIndex], this.editedItem);
      } else {
        var formdata = new FormData();

        formdata.append("id", this.editedItem.id);
        formdata.append("nombre", this.editedItem.nombre);
        formdata.append("telefono", this.editedItem.telefono);
        formdata.append("email", this.editedItem.email);
        formdata.append("cuil", this.editedItem.cuil);

        var requestOptions = {
          method: "POST",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/proveedores.php",
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result));
        // this.desserts.push(this.editedItem);
        this.initialize();
      }
      this.close();
    },
  },
};
</script>
