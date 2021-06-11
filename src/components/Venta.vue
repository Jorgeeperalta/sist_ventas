<template>
  <v-app>
    <v-card class="mx-auto" width="1150">
      <v-text-field color="purple darken-4" loading disabled></v-text-field>
      <v-toolbar color="purple darken-4">
        <v-app-bar-nav-icon class="hidden-sm-and-down"></v-app-bar-nav-icon>
        <v-toolbar-title class="title mr-6 hidden-sm-and-down"
          >Producto</v-toolbar-title
        >

        <v-card width="400">
          <v-text-field
            v-model="identificador"
            v-on:keyup.enter="guardar(cont++)"
            loading
            color="light-green accent-3"
          ></v-text-field>
        </v-card>
        <v-spacer></v-spacer>
        <v-toolbar-title class="title mr-6 hidden-sm-and-down"
          >Cantidad</v-toolbar-title
        >
        <v-card width="100">
          <v-text-field
            v-model="totales"
            v-on:keyup.enter="guardar(cont++)"
            loading
            color="light-green accent-3"
          ></v-text-field>
        </v-card>
      </v-toolbar>

      <v-text-field color="purple darken-4" loading disabled></v-text-field>
    </v-card>

    <template>
      <v-container class="pa-4 text-center">
        <v-row class="fill-height" justify="center">
          <v-spacer></v-spacer>

          <v-col cols="12" sm="8">
            <v-hover v-slot:default="{ hover }" open-delay="200">
              <v-card
                :elevation="hover ? 16 : 2"
                class="mx-auto"
                height="1600"
                width="1000"
              >
                <v-card width="1000">
                  <v-data-table
                    :headers="headers"
                    :items="productos"
                    sort-by="precio"
                    class="elevation-1"
                    :custom-filter="filterOnlyCapsText"
                  >
                    <template v-slot:item.actions="{ item }">
                      <v-icon
                        small
                        class="mr-2"
                        @click="editItem(item)"
                        color="yellow accent-2"
                      >
                        mdi-pencil
                      </v-icon>
                      <v-icon
                        color="pink accent-4"
                        small
                        @click="deleteItem(item)"
                      >
                        mdi-delete
                      </v-icon>
                    </template>
                    <template v-slot:no-data>
                      <v-btn color="primary" @click="initialize">Reset</v-btn>
                    </template>
                  </v-data-table>
                </v-card>
              </v-card>
            </v-hover>
          </v-col>

          <v-col cols="12" sm="4">
            <v-hover v-slot:default="{ hover }" close-delay="200">
              <v-card
                :elevation="hover ? 16 : 2"
                class="mx-auto"
                height="350"
                width="350"
              >
                <v-card width="350">
                  <v-toolbar color="purple darken-4" dark>
                    <v-btn color="pink accent-4" icon>
                      <v-icon> mdi-delete</v-icon>
                    </v-btn>
                    <v-toolbar-title>Eliminar compra</v-toolbar-title>
                  </v-toolbar>
                  <v-label>Total </v-label>
                  <h1>$ {{ monto }}</h1>
                  <v-container>
                    <v-row justify="center">
                      <v-col md="6" sm="6" xl="6">
                        <v-text-field
                          color="light-green accent-3"
                          loading
                          label="Dinero recibido"
                          v-model="efectivo"
                          v-on:keyup.enter="resumen()"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-container>

                  <v-label>Vuelto </v-label>
                  <h1>$ {{ fin }}</h1>
                  <v-btn
                    class="mx-2"
                    fab
                    dark
                    large
                    color="deep-orange darken-4"
                    @click="print(), limpiar()"
                    ><v-icon>mdi-shredder</v-icon></v-btn
                  >
                </v-card>
              </v-card>
            </v-hover>
          </v-col>
        </v-row>
      </v-container>
    </template>
  </v-app>
</template>

<script>
import jsPDF from "jspdf";

export default {
  data: () => ({
    fin: 0,
    efectivo: "",
    monto: 0,
    cont: 0,
    identificador: "",
    isLoading: false,
    items: [],
    productos: [
      {
        id: "",
        nombre: "",
        precio: "",
        cantidad: "",
        sub_total: "",
      },
    ],
    editedItem: [],
    model: null,
    dialog: false,
    search: null,
    headers: [
      {
        text: "Identificador",
        align: "start",
        sortable: false,
        value: "id",
      },

      { text: "Nombre", value: "nombre" },
      { text: "Precio", value: "precio" },

      { text: "Cantidad", value: "cantidad" },
      { text: "Sub Total", value: "sub_total" },
      { text: "Actions", value: "actions", sortable: false },
    ],

    totales: 1,
  }),
  created() {
    fetch("http://jorgeperalta-001-site6.itempurl.com/articulos.php")
      .then((res) => res.clone().json())
      .then((res) => {
        this.items = res;
      })
      .catch((err) => {
        console.log(err);
      })
      .finally(() => (this.isLoading = false));
  },

  watch: {
    model(val) {},

    search(val) {
      // Items have already been loaded
      if (this.items.length > 0) return;

      this.isLoading = true;

      // Lazily load input items
      fetch("http://jorgeperalta-001-site6.itempurl.com/articulos.php")
        .then((res) => res.clone().json())
        .then((res) => {
          this.items = res;
        })
        .catch((err) => {
          console.log(err);
        })
        .finally(() => (this.isLoading = false));
    },
  },

  methods: {
    print() {
      var horaA = new Date();
      let pdfName = "Factura ";
      var contador = 10;
      var doc = new jsPDF();
      doc.text(20, 5, "Fecha  " + horaA);
      doc.text(20, 10, "Producto        P.U.      Cant.       Subtotal" );
      this.productos.forEach((element) => {
        doc.text(20, contador, " " + element.nombre+"    "+element.precio+"       "+element.cantidad+"       "+element.sub_total);
        contador = contador + 10;
      });
         contador=contador+10;
         doc.text(60,contador,"Total $  "+this.monto);
          contador=contador+10;
           doc.text(60,contador,"Entrego $  "+this.efectivo);
              contador=contador+10;
           doc.text(60,contador,"Su vuelto $  "+this.fin);
      doc.save(pdfName + ".pdf");
    },
    limpiar() {
      this.productos = [];
      this.fin = "";
      this.monto = "";
      this.efectivo = "";
      this.identificador = "";
    },
    resumen() {
      this.fin = this.efectivo - this.monto;
    },
    deleteItem(item) {
      console.log(item.id);
      this.monto = this.monto - item.sub_total;

      const index = this.productos.indexOf(item);
      confirm("Esta seguro de eliminar este producto?") &&
        this.productos.splice(index, 1);
    },
    guardar(cont) {
      this.items.forEach((element) => {
        if (this.identificador == element.id) {
          this.productos.push({
            id: cont,
            nombre: element.nombre,
            precio: element.precio,
            cantidad: this.totales,
            sub_total: this.totales * element.precio,
          });
          cont++;
          this.monto += this.totales * element.precio;
        }
      });
    },
    getColor(sub_total) {
      if (sub_total > 400) return "purple darken-3";
      else if (sub_total > 200) return "teal darken-2";
      else return "blue darken-2";
    },
    initialize() {},
    filterOnlyCapsText(value, search, item) {
      return (
        value != null &&
        search != null &&
        typeof value === "string" &&
        value
          .toString()
          .toLocaleUpperCase()
          .indexOf(search) !== -1
      );
    },
  },
};
</script>
