<template>
  <v-card class="mx-auto" width="1200">
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
          <v-toolbar-title>Clientes</v-toolbar-title>

          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-text-field
           :custom-filter="filterOnlyCapsText"
            v-model="search"
            label="Buscar (POR NOMBRE)"
            class="mx-2"
          ></v-text-field>

          <v-spacer></v-spacer>
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
                    <v-col cols="12" sm="4" md="4">
                      <v-text-field
                        v-model="editedItem.apellido"
                        label="Apellido"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="4" md="4">
                      <v-text-field
                        v-model="editedItem.nombre"
                        label="Nombre"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="4" md="4">
                      <v-text-field
                        v-model="editedItem.telefono"
                        label="Telefono"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" sm="4" md="4">
                      <v-select
                        :items="coordenadaslatlong"
                        item-text="loc"
                        item-value="'loc'"
                        label="Seleccione Localidad"
                        v-model="editedItem.localidad"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" sm="4" md="4">
                      <v-text-field
                        v-model="editedItem.domicilio"
                        label="Domicilio"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="4" md="4">
                      <v-text-field
                        v-model="editedItem.email"
                        label="Email"
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
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
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
    coordenadaslatlong: [
      { loc: "SAN CLEMENTE DEL TUYú", lat: -36.3560141, lng: -56.7189854 },
      { loc: "LAS TONINAS", lat: -36.4882488, lng: -56.7050737 },
      { loc: "COSTA CHICA", lat: -36.5083451, lng: -56.6979825 },
      { loc: "SANTA TERESITA", lat: -36.5431401, lng: -56.7043746 },
      { loc: "MAR DEL TUYú", lat: -36.5813187, lng: -56.6874786 },
      { loc: "COSTA DEL ESTE", lat: -36.6115002, lng: -56.6881643 },
      { loc: "AGUAS VERDES", lat: -36.6371259, lng: -56.6848594 },
      { loc: "LUCILA DEL MAR", lat: -36.66667, lng: -56.66667 },
      { loc: "SAN BERNARDO", lat: -36.6865934, lng: -56.6841459 },
      { loc: "MAR DE AJó", lat: -36.7347632, lng: -56.6903612 },
      { loc: "NUEVA ATLANTIS", lat: -36.7634299, lng: -56.6765584 },
      { loc: "PUNTA MEDANOS", lat: -36.9014655, lng: -56.6886537 },
    ],

    idd: "",
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
      { text: "Apellido", value: "apellido" },
      { text: "Nombre", value: "nombre" },
      { text: "Telefono", value: "telefono" },
      { text: "Localidad", value: "localidad" },
      { text: "Domicilio", value: "domicilio" },
      { text: "Email", value: "email" },

      { text: "Actions", value: "actions", sortable: false },
    ],
    desserts: [],
    editedIndex: -1,
    editedItem: {
      id: "",
      apellido: "",
      nombre: "",
      telefono: "",
      localidad: "",
      domicilio: "",
      email: "",
    },
    defaultItem: {
      id: "",
      apellido: "",
      nombre: "",
      telefono: "",
      localidad: "",
      domicilio: "",
      email: "",
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Item" : "Edita Item";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
  },

  created() {
    this.initialize();
  },

  methods: {
       filterOnlyCapsText(value, search, item) {
      return (
        value != null &&
        search != null &&
        typeof value === "string" &&
        value.toString().toLocaleUpperCase().indexOf(search) !== -1
      );
    },
    initialize() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/clientes.php",
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

    deleteItem(item) {
      const index = this.desserts.indexOf(item);
      confirm("Esta seguro de eliminar este item?") &&
        this.desserts.splice(index, 1);

      var requestOptions = {
        method: "DELETE",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/clientes.php?id=" + item.id,
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
          "http://jorgeperalta-001-site6.itempurl.com/clientes.php?id=" +
            this.editedItem.id +
            "&apellido=" +
            this.editedItem.apellido +
            "&nombre=" +
            this.editedItem.nombre +
            "&telefono=" +
            this.editedItem.telefono +
            "&localidad=" +
            this.editedItem.localidad +
            "&domicilio=" +
            this.editedItem.domicilio +
            "&email=" +
            this.editedItem.email,
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result));
        this.initialize();
        //  Object.assign(this.desserts[this.editedIndex], this.editedItem);
      } else {
        var formdata = new FormData();

        formdata.append("id", "");
        formdata.append("apellido", this.editedItem.apellido);
        formdata.append("nombre", this.editedItem.nombre);
        formdata.append("telefono", this.editedItem.telefono);
        formdata.append("localidad", this.editedItem.localidad);
        formdata.append("domicilio", this.editedItem.domicilio);
        formdata.append("email", this.editedItem.email);

        var requestOptions = {
          method: "POST",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/clientes.php",
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result));
        // this.desserts.push(this.editedItem);
      }
      this.initialize();
      this.close();
    },
  },
};
</script>
