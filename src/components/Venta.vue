<template>
  <v-app>
    <v-card class="mx-auto" width="1150">
      <br />
      <v-label><h1 style="margin-left: 10px">Ventas</h1></v-label>

      <v-row>
        <v-col md="6">
          <v-autocomplete
            style="margin-left: 10px; margin-top: 30px"
            v-model="value"
            :items="clientes"
            item-text="apellido"
            item-value="id"
            dense
            filled
            label="Buscar por apellido"
          ></v-autocomplete>
        </v-col>
        <v-col md="6" style="margin-top: 25px">
          <div style="margin-left: 20px" @click="asignacliente(value)">
            Apellido y Nombre: <b> {{ customer.apellido }} </b>
            <v-spacer></v-spacer> Localidad: <b> {{ customer.localidad }} </b>
            <v-spacer></v-spacer> Domicilio: <b> {{ customer.domicilio }} </b>
            <v-spacer></v-spacer> Telefono: <b> {{ customer.telefono }} </b>
          </div>
        </v-col>
      </v-row>

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
            color="light-green accent-3"
          ></v-text-field>
        </v-card>
        <v-spacer></v-spacer>
        <v-toolbar-title class="title mr-6 hidden-sm-and-down"
          >Cantidad</v-toolbar-title
        >
        <v-card width="100">
          <v-text-field
            ref="cantidad"
            v-model="totales"
            v-on:keyup.enter="guardar(cont++), focusInput()"
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

          <v-col cols="12" md="8" @click="guardarcta(cuen++)">
            <v-hover v-slot:default="{ hover }" open-delay="200">
              <v-card
                :elevation="hover ? 16 : 2"
                class="mx-auto"
                height="1600"
                width="1000"
              >
                <v-card width="1000">
                  <v-data-table
                    v-model="selected"
                    :headers="headers"
                    :items="productos"
                    :single-select="singleSelect"
                    item-key="nombre"
                    show-select
                    sort-by="precio"
                    class="elevation-1"
                    :custom-filter="filterOnlyCapsText"
                  >
                    <template v-slot:[`item.actions`]="{ item }">
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
                    <template v-slot:top>
                      <v-switch
                        v-model="singleSelect"
                        label="Single select"
                        class="pa-3"
                      ></v-switch>
                    </template>
                  </v-data-table>
                </v-card>
              </v-card>
            </v-hover>
          </v-col>

          <v-col cols="12" sm="4">
            <br />
            <div>
              <v-btn
                style="margin-top: -8px; margin-left: 0px"
                outlined
                color="green"
                @click="(cta = false), (ahora = true), (acopio = false)"
                >Ahora</v-btn
              >
              <v-btn
                style="margin-top: -8px; margin-left: 20px"
                @click="
                  ((cta = true), (ahora = false), (acopio = false)),
                    guardarcta(cuen++)
                "
                outlined
                color="yellow"
                >Cta. Cte.</v-btn
              >
              <v-btn
                style="margin-top: -8px; margin-left: 20px"
                @click="(cta = false), (ahora = false), (acopio = true)"
                outlined
                color="red"
                >Acopio</v-btn
              >
            </div>
            <br />
            <v-label v-if="ahora == true"><h1>Ahora</h1> </v-label>
            <v-label v-if="cta == true"><h1>Cta Cte</h1> </v-label>
            <v-label v-if="acopio == true"><h1>Acopio</h1> </v-label>
            <br />
            <!--    Ahora    -->
            <v-hover
              v-slot:default="{ hover }"
              close-delay="200"
              v-if="ahora == true"
            >
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
                    <v-label>
                      <h2 style="margin-left: 90px">Total</h2>
                    </v-label>
                  </v-toolbar>

                  <h1>$ {{ monto }}</h1>

                  <v-spacer></v-spacer>
                  <v-col md="12">
                    <v-select
                      :items="tiposdepago"
                      v-model="tipopago"
                    ></v-select>
                  </v-col>
                  <div v-if="tipopago == 'Contado'">
                    <v-container>
                      <v-row justify="center">
                        <v-col md="6" sm="6" xl="6">
                          <v-text-field
                            color="light-green accent-3"
                            loading
                            label="Dinero recibido"
                            v-model="efectivo"
                            v-on:keyup.enter="resumen(), mu()"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-container>

                    <v-label>Vuelto </v-label>
                    <h1>$ {{ fin }}</h1>
                  </div>
                  <v-row>
                    <v-col md="12">
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="green darken-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      >

                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="amber accent-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      >
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="deep-orange darken-4"
                        @click="print()"
                        ><v-icon>mdi-shredder</v-icon></v-btn
                      >
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="red"
                        @click="limpiar()"
                        ><v-icon>mdi-broom</v-icon></v-btn
                      >
                    </v-col>
                  </v-row>
                </v-card>
              </v-card>
            </v-hover>
            <!--    Ahora  Fin  -->

            <!--    Cta Cte    -->
            <v-hover
              v-slot:default="{ hover }"
              close-delay="200"
              v-if="cta == true"
            >
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
                    <v-label>
                      <h2 style="margin-left: 90px">Total</h2>
                    </v-label>
                  </v-toolbar>

                  <h1>$ {{ montocta }}</h1>

                  <v-spacer></v-spacer>
                  <v-select :items="tiposdepago" v-model="tipopago"></v-select>
                  <div v-if="tipopago == 'Contado'">
                    <v-container>
                      <v-row justify="center">
                        <v-col md="6" sm="6" xl="6">
                          <v-text-field
                            color="light-green accent-3"
                            loading
                            label="Dinero recibido"
                            v-model="efectivo"
                            v-on:keyup.enter="resumen(), mu()"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-container>

                    <v-label>Vuelto </v-label>
                    <h1>$ {{ fin }}</h1>
                  </div>
                  <v-row>
                    <v-col md="12">
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="green darken-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      >

                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="amber accent-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      >
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="deep-orange darken-4"
                        @click="print()"
                        ><v-icon>mdi-shredder</v-icon></v-btn
                      >
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="red"
                        @click="limpiar()"
                        ><v-icon>mdi-broom</v-icon></v-btn
                      >
                    </v-col>
                  </v-row>
                </v-card>
              </v-card>
            </v-hover>
            <!--    Cta Cte  Fin  -->

            <!--    Acopio    -->
            <v-hover
              v-slot:default="{ hover }"
              close-delay="200"
              v-if="acopio == true"
            >
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
                    <v-label>
                      <h2 style="margin-left: 90px">Total</h2>
                    </v-label>
                  </v-toolbar>

                  <h1>$ {{ monto }}</h1>

                  <v-spacer></v-spacer>
                  <v-select :items="tiposdepago" v-model="tipopago"></v-select>
                  <div v-if="tipopago == 'Contado'">
                    <v-container>
                      <v-row justify="center">
                        <v-col md="6" sm="6" xl="6">
                          <v-text-field
                            color="light-green accent-3"
                            loading
                            label="Dinero recibido"
                            v-model="efectivo"
                            v-on:keyup.enter="resumen(), mu()"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-container>

                    <v-label>Vuelto </v-label>
                    <h1>$ {{ fin }}</h1>
                  </div>
                  <v-label>Fecha estimada de retiro</v-label>
                  <br />
                  <date-picker v-model="time1" valueType="format"></date-picker>

                  <v-row>
                    <v-col md="12">
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="green darken-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      >

                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="amber accent-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      >
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="deep-orange darken-4"
                        @click="print(), limpiar()"
                        ><v-icon>mdi-shredder</v-icon></v-btn
                      >
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="red"
                        @click="limpiar()"
                        ><v-icon>mdi-broom</v-icon></v-btn
                      >
                    </v-col>
                  </v-row>
                </v-card>
              </v-card>
            </v-hover>
            <!--    Acopio  Fin  -->
          </v-col>
        </v-row>

        <v-btn @click="mu()"> muestra</v-btn>
      </v-container>
    </template>
  </v-app>
</template>

<script>
import jsPDF from "jspdf";
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";

export default {
  components: { DatePicker },
  data: () => ({
    time1: null,
    tiposdepago: ["Contado", "Targeta", "Cheque", "Cta. DNI", "Mercado Pago"],
    tipopago: null,
    totalcta: null,
    cuen: 0,
    montocta: 0,
    fincta: 0,
    ahora: false,
    cta: false,
    acopio: false,
    singleSelect: false,
    selected: [],
    value: null,
    customer: [],
    fin: 0,
    efectivo: "",
    monto: 0,
    cont: 0,
    identificador: null,
    isLoading: false,
    items: [],
    clientes: [],
    strarticulos: "",
    productos: [],
    editedItem: [],
    model: null,
    dialog: false,
    search: null,
    productoscta: [],
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

    this.cargaclientes();
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
    recargaarticulos(){
        fetch("http://jorgeperalta-001-site6.itempurl.com/articulos.php")
        .then((res) => res.clone().json())
        .then((res) => {
          this.items = res;
        })
        .catch((err) => {
          console.log(err);
        })
    },
    guardarventa() {
      console.log(this.productos);
      if (this.strarticulos != "") {
        var formdata = new FormData();

        formdata.append("id", "");
        formdata.append("fkcliente", this.customer.id);
        formdata.append("fecha", new Date().toISOString().substr(0, 10));
        formdata.append("monto", this.monto);
        formdata.append("tipopago", this.tipopago);
        formdata.append("strarticulos", this.strarticulos);

        var requestOptions = {
          method: "POST",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/ventas.php",
          requestOptions
        )
          .then(function (response) {
            if (response.ok) {
              //  console.log(  response.text());
              confirm("La venta se almaceno con exito!!");
             
          
            } else {
              console.log("Respuesta de red OK pero respuesta HTTP no OK");
            }
          })

          .catch(function (error) {
            confirm(
              "Hubo un problema con la red, intente nuevamente:" + error.message
            );
          });
      } else {
        confirm("No cuenta con ninguna venta");
      }
          this.restastock(); 
    },
    restastock(){
      this.productos.forEach((element) => {
        console.log(element)
      var formdata = new FormData();

      formdata.append("id", element.pk);
      formdata.append("resta", element.cantidad);

      var requestOptions = {
        method: "POST",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/actualizastock.php",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result));
      });
      this.recargaarticulos()
    },
    focusInput() {
      this.$refs.cantidad.focus();
    },
    mu() {
      console.log(this.selected);
    },
    asignacliente(id) {
      this.clientes.forEach((element) => {
        if (element.id == id) {
          this.customer = element;
        }
      });
    },
    cargaclientes() {
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
        .then((result) => (this.clientes = result))
        .catch((error) => console.log("error", error));
    },
    print() {
      //  this.productos.forEach((element) => {
      //    this.productos.push(element)
      //  })
      var horaA = new Date().toISOString().substr(0, 10);
      var arrayFeha = horaA.split(["-"]);
      let pdfName = "Factura ";
      var contador = 20;
      var doc = new jsPDF();
      doc.text(
        20,
        5,
        "Fecha  " + arrayFeha[2] + "-" + arrayFeha[1] + "-" + arrayFeha[0]
      );
      doc.text(20, 10, "Producto        P.U.      Cant.       Subtotal");
      doc.text(40, 40, "");
      this.productos.forEach((element) => {
        doc.text(
          20,
          contador,
          " " +
            element.nombre +
            "    " +
            element.precio +
            "       " +
            element.cantidad +
            "       " +
            element.sub_total
        );
        contador = contador + 10;
      });
      contador = contador + 10;
      doc.text(60, contador, "Total $  " + Math.round(this.monto));
      contador = contador + 10;
      doc.text(60, contador, "Entrego $  " + this.efectivo);
      contador = contador + 10;
      doc.text(60, contador, "Su vuelto $  " + Math.round(this.fin));
      doc.save(pdfName + ".pdf");
    },
    limpiar() {
      this.productos = [];
      this.fin = 0;
      this.monto = 0;
      this.efectivo = 0;
      this.identificador = "";
      this.customer = [];
      this.tipopago = "";
      this.strarticulos = "";
      this.value = "";
    },
    resumen() {
      this.fin = this.efectivo - this.monto;
    },
    deleteItem(item) {
      console.log(item.id);
      this.monto = Math.round(this.monto - item.sub_total);

      const index = this.productos.indexOf(item);
      confirm("Esta seguro de eliminar este producto?") &&
        this.productos.splice(index, 1);
    },

    guardarcta(cont) {
      this.productoscta = [];
      this.montocta = 0;
      this.selected.forEach((element) => {
        this.productoscta.push({
         
          id: cont,
          nombre: element.nombre,
          precio: Math.round(element.precio),
          cantidad: element.cantidad,
          sub_total: Math.round(element.cantidad * element.precio),
        });
        cont++;
        this.montocta += Math.round(element.cantidad * element.precio);
      });

      // this.productoscta.forEach((element) => {
      //   console.log(element);
      // });

      //  console.log(this.montocta);
    },
    guardar(cont) {
      this.items.forEach((element) => {
        if (this.identificador == element.id) {
          if (element.cantidad == "0") {
            confirm("No tiene stock disonible de " + element.nombre);
          } else if (
            this.totales == element.cantidad ||
            this.totales < element.cantidad
          ) {
            this.productos.push({
              pk:element.id,
              id: cont,
              nombre: element.nombre,
              precio: Math.round(element.precio),
              cantidad: this.totales,
              sub_total: Math.round(this.totales * element.precio),
            });
            this.strarticulos +=
              this.totales +
              "  " +
              element.nombre +
              "  " +
              element.precio +
              "-" +
              "\n";
            cont++;
            this.monto += Math.round(this.totales * element.precio);
          } else if (element.cantidad < this.totales) {
            confirm(
              "La cantidad requerida excede lo disponible  " +
                element.nombre +
                "  " +
                element.cantidad +
                " unidades en stock"
            );
          }
          if (
            this.stockminimo == element.cantidad ||
            this.stockminimo < element.cantidad
          ) {
            confirm("Debe comprar el articulo " + element.nombre);
          }
        }
      });

      //  console.log(this.selected);
      this.identificador = null;
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
        value.toString().toLocaleUpperCase().indexOf(search) !== -1
      );
    },
  },
};
</script>
