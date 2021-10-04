<template>
  <v-app>
    <v-card class="mx-auto" width="1150">
      <v-snackbar
        style="margin-top: 20px"
        v-model="snackbar"
        :timeout="timeout"
        color="blue"
      >
        {{ tipopago }} {{ montomoneda }}
      </v-snackbar>
        <v-snackbar
        style="margin-top: 20px"
        v-model="snackbar2"
        :timeout="timeout"
        color="green"
      >
        Venta almacenada con exito!!
      </v-snackbar>
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

      <div>
        <v-row>
          <v-col md="4">
            <v-btn
              block
              style="margin-top: -8px; margin-left: 10px"
              @click="
                ((presupuesto = true),
                (ahora = false),
                (acopio = false),
                print()),
                  guardarcta(cuen++)
              "
              outlined
              color="yellow"
              >Presupuesto</v-btn
            >
          </v-col>

          <v-col md="4">
            <v-btn
              block
              style="margin-top: -8px; margin-left: 2px"
              outlined
              color="green"
              @click="(presupuesto = false), (ahora = true), (acopio = false)"
              >Retira Ahora</v-btn
            >
          </v-col>
          <v-col md="">
            <v-btn
              block
              style="margin-top: -8px; margin-left: -5px"
              @click="(presupuesto = false), (ahora = false), (acopio = true)"
              outlined
              color="red"
              >Acopio</v-btn
            >
          </v-col>
        </v-row>

        <!-- <v-btn
                style="margin-top: -8px; margin-left: 20px"
                @click="
                  ((cta = true), (ahora = false), (acopio = false)),
                    guardarcta(cuen++)
                "
                outlined
                color="yellow"
                >Cta. Cte.</v-btn
              > -->
      </div>

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

          <v-col md="4" sm="4">
            <!--    Ahora    -->
            <v-hover
              style="margin-top: 15px"
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
                    <!-- <v-btn color="pink accent-4" icon>
                      <v-icon> mdi-delete</v-icon>
                    </v-btn> -->
                    <v-col md="12">
                      <v-label v-if="ahora == true"
                        ><h1>*******Ahora*******</h1>
                      </v-label>
                      <v-label v-if="cta == true"><h1>Cta Cte</h1> </v-label>
                      <v-label v-if="acopio == true"><h1>Acopio</h1> </v-label>
                    </v-col>
                  </v-toolbar>

                  <h1>Total $ {{ monto }}</h1>
                  <v-radio-group v-model="opcion">
                    <v-row>
                      <v-col md="4">
                        <v-radio
                          style="margin-left: 20px"
                          value="Simple"
                          label="Simple"
                        ></v-radio>
                      </v-col>
                      <v-col md="4">
                        <v-radio value="Mixto" label="Mixto"></v-radio>
                      </v-col>
                       <v-col md="4">
                        <v-radio value="Debe" label="Debe"></v-radio>
                      </v-col>
                    </v-row>
                  </v-radio-group>
                  <v-spacer></v-spacer>
                  <v-col md="12" v-if="opcion!='Debe'">
                    <v-select
                    label="Tipo de pago"
                      :items="tiposdepago"
                      v-model="tipopago"
                    ></v-select>
                    <div v-if="opcion == 'Mixto'">
                      <v-text-field
                        v-model="montomoneda"
                        :label="tipopago"
                      ></v-text-field>
                      <v-btn
                        block
                        color="purple"
                        outlined
                        @click="agregatipopago(), (snackbar = true)"
                        >Guardar Forma de pago</v-btn
                      >
                    </div>
                  </v-col>
                  <!-- <div v-if="tipopago == 'Efectivo'">
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
                  </div> -->
                  <v-row>
                    <v-col md="12">
                      <v-btn
                        outlined
                        class="mx-2"
                        fab
                        dark
                        large
                        color="green darken-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-database-plus</v-icon></v-btn
                      >

                      <!-- <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="amber accent-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      > -->
                      <v-btn
                        outlined
                        class="mx-2"
                        fab
                        dark
                        large
                        color="deep-orange darken-4"
                        @click="print()"
                        ><v-icon>mdi-shredder</v-icon></v-btn
                      >
                      <v-btn
                        outlined
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
                    <!-- <v-container>
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
                    </v-container> -->

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

                      <!-- <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="amber accent-3"
                        @click=" guardarcta()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      > -->
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
              style="margin-top: 15px"
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
                    <!-- <v-btn color="pink accent-4" icon>
                      <v-icon> mdi-delete</v-icon>
                    </v-btn> -->
                    <v-label v-if="ahora == true"
                      ><h1>*****Ahora*****</h1>
                    </v-label>
                    <v-label v-if="cta == true"><h1>Cta Cte</h1> </v-label>
                    <v-label v-if="acopio == true"
                      ><h1>*******Acopio******</h1>
                    </v-label>
                  </v-toolbar>

                  <h1>Total $ {{ monto }}</h1>

                   <v-radio-group v-model="opcion">
                    <v-row>
                      <v-col md="4">
                        <v-radio
                          style="margin-left: 20px"
                          value="Simple"
                          label="Simple"
                        ></v-radio>
                      </v-col>
                      <v-col md="4">
                        <v-radio value="Mixto" label="Mixto"></v-radio>
                      </v-col>
                       <v-col md="4">
                        <v-radio value="Debe" label="Debe"></v-radio>
                      </v-col>
                    </v-row>
                  </v-radio-group>
                  <v-spacer></v-spacer>
                  <v-col md="12" v-if="opcion!='Debe'">
                    <v-select
                      label="Tipo de pago"
                      :items="tiposdepago"
                      v-model="tipopago"
                    ></v-select>
                    <div v-if="opcion == 'Mixto'">
                      <v-text-field
                        v-model="montomoneda"
                        :label="tipopago"
                      ></v-text-field>
                      <v-btn
                        block
                        color="purple"
                        outlined
                        @click="agregatipopago(), (snackbar = true)"
                        >Guardar Forma de pago</v-btn
                      >
                    </div>
                  </v-col>
                  <!-- <div v-if="tipopago == 'Efectivo'">
                    <v-container   >
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
                  </div> -->
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

                      <!-- <v-btn
                        class="mx-2"
                        fab
                        dark
                        large
                        color="amber accent-3"
                        @click="guardarventa()"
                        ><v-icon>mdi-currency-usd</v-icon></v-btn
                      > -->
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

        <!-- <v-btn @click="mu()"> muestra</v-btn> -->
      </v-container>
    </template>
  </v-app>
</template>

<script>
import jsPDF from "jspdf";
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import imagen from "@/imagen"
export default {
  components: { DatePicker },
  data: () => ({
    opcion: "Simple",
    numerodeventa: 0,
    salidapost: "",
    auxtipoventa: "",
    auxfecharetiro: "",
    time1: null,
    tiposdepago: [
      "Efectivo",
      "Targeta",
      "Cheque",
      "Cta. DNI",
      "Mercado Pago",
      "Transferencia",
     
    ],
    tipopago:'Efectivo',
    totalcta: null,
    cuen: 0,
    montocta: 0,
    fincta: 0,
    ahora: false,
    cta: false,
    acopio: false,
    singleSelect: false,
    timeout: 2000,
    selected: [],
     snackbar: false,
    snackbar2: false,
    value: null,
    customer: [],
    fin: 0,
    efectivo: "",
    monto: 0,
    cont: 0,
    moneda: [],
    montomoneda: 0,
    identificador: null,
    isLoading: false,
    items: [],
    clientes: [],
    presupuesto: false,
    text: `Hello, I'm a snackbar`,
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
    agregatipopago() {
      this.moneda.push({
        tipopago: this.tipopago,
        cantidad: this.montomoneda,
      });
    },
    guardaopcion() {
      
    //  console.log(this.salidapost.idventa);
      if (this.opcion == "Mixto") {
        this.moneda.forEach((element) => {
          var formdata = new FormData();

          formdata.append("id", "");
          formdata.append("tipo", element.tipopago);
          formdata.append("pesos", element.cantidad);
          formdata.append("fkventa", this.salidapost.idventa);

          var requestOptions = {
            method: "POST",
            body: formdata,
            redirect: "follow",
          };

          fetch(
            "http://jorgeperalta-001-site6.itempurl.com/mixto.php",
            requestOptions
          )
            .then((response) => response.json())
            // .then((result) => console.log(result))
            .then(
              (result) => console.log(result)
              //  confirm("La venta se almaceno con exito!!")
            )
            .catch((error) => console.log("error", error));
        });
      }else{

        var auxtipopago= this.tipopago
         var formdata = new FormData();

          formdata.append("id", "");
          formdata.append("tipo", auxtipopago);
          formdata.append("pesos", this.monto);
          formdata.append("fkventa", this.salidapost.idventa);

          var requestOptions = {
            method: "POST",
            body: formdata,
            redirect: "follow",
          };

          fetch(
            "http://jorgeperalta-001-site6.itempurl.com/mixto.php",
            requestOptions
          )
            .then((response) => response.json())
            // .then((result) => console.log(result))
            .then(
              (result) => console.log(result)
              //  confirm("La venta se almaceno con exito!!")
            )
            .catch((error) => console.log("error", error));
      }
    },
    recargaarticulos() {
      fetch("http://jorgeperalta-001-site6.itempurl.com/articulos.php")
        .then((res) => res.clone().json())
        .then((res) => {
          this.items = res;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    guardarventa() {
      if (this.acopio == true) {
        this.auxtipoventa = "Acopio";
        this.auxfecharetiro = this.time1;
      } else if (this.cta == true) {
        this.auxtipoventa = "cta cte";
      } else if (this.ahora == true) {
        this.auxtipoventa = "Retirado";
        this.auxfecharetiro = new Date().toISOString().substr(0, 10);
      }
      console.log(this.productos)
      this.productos.forEach(element =>{
         this.strarticulos +=
              element.cantidad +
              "  " +
              element.nombre +
              "  " +
              element.precio +
              "-" +
              "\n";
      })
      console.log(this.strarticulos)
      
      if (this.strarticulos != "") {
        var formdata = new FormData();

        formdata.append("id", "");
        formdata.append("fkcliente", this.customer.id);
        formdata.append("fecha", new Date().toISOString().substr(0, 10));
        formdata.append("monto", this.monto);
        formdata.append("tipopago", this.opcion);
        formdata.append("strarticulos", this.strarticulos);
        formdata.append("tipoventa", this.auxtipoventa);
        formdata.append("fecharetiro", this.auxfecharetiro);
        formdata.append("fechasalida", this.auxfecharetiro);

        async function asyncData() {
         
          const response = await fetch(
            "http://jorgeperalta-001-site6.itempurl.com/ventas.php",
            { method: "POST", body: formdata }
          );
          const data = await response.json();

          return data;
        }

        const result = asyncData();
        
        result.then((data) => {
          this.salidapost = data;

         // console.log(this.salidapost);
          this.guardaopcion();
          this.snackbar2=true
            this.restastock();
        })
        result.catch(error => {alert('error de red '+error)})
      } else {
        confirm("No cuenta con ninguna venta");
      }
      // if(this.salidapost==""){
      //   alert("No se almaceno la venta, error en la red")
      // }
      // this.restastock();
    },
    
    restastock() {
      if( this.auxtipoventa!= "Acopio"){

    
      this.productos.forEach((element) => {
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

        }else if(this.auxtipoventa=='Acopio'){
         
              this.productos.forEach((element=>{

     
      var formdata = new FormData();

      formdata.append("id", "");

      formdata.append("detalle", element.nombre);
      formdata.append("fkventa", this.salidapost.idventa);
      formdata.append("fecha", new Date().toISOString().substr(0, 10));
      formdata.append("cantidad", element.cantidad);
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/detalleacopio.php",
          { method: "POST", body: formdata }
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        console.log(data);
       
      });
       }))
        }

      this.recargaarticulos();
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
      console.log(this.salidapost);
      //this.numerodeventa=this.salidapost.idventa;
      var horaA = new Date().toISOString().substr(0, 10);
      var arrayFeha = horaA.split(["-"]);
      let pdfName = "Factura ";
      var contador = 20;
      var doc = new jsPDF();
      doc.setLineWidth(1.5);
      doc.line(8, 5, 200, 5);
      var image =imagen;
      doc.addImage(image, "PNG", 85, 6, 40, 40);
      doc.line(10, 35, 200, 35);
      doc.setFontSize(14);
      doc.text(
        150,
        12,
        "Fecha:  " + arrayFeha[2] + "-" + arrayFeha[1] + "-" + arrayFeha[0]
      );
      if (this.presupuesto == true) {
        doc.setFontSize(14);
        doc.text(10, 12, "Presupuesto");
      } else {
        doc.text(10, 12, "Remito:  " + this.salidapost.idventa);
      }
      doc.setFontSize(8);
      doc.text(150, 30, "Uso interno");
      doc.setFontSize(13);
      doc.text(10, 47, "Condicion de IVA : Consumidor Final  ");
      doc.setFontSize(13);
      if (this.presupuesto != true) {
        doc.text(130, 47, "Tipo de Pago:  " + this.opcion);
      }
      doc.setFontSize(13);
      doc.text(10, 57, "Apellido y Nombre:  " + this.customer.apellido);
      doc.setFontSize(13);
      doc.text(130, 57, "Domicilio:  " + this.customer.domicilio);
      doc.setFontSize(13);
      doc.text(10, 67, "Localidad:  " + this.customer.localidad);
      doc.setFontSize(13);
      if (this.presupuesto == true) {
        doc.text(130, 67, "Presupuesto");
      } else {
        doc.text(130, 67, "Retira: " + this.auxtipoventa);
      }

      if (this.auxtipoventa == "Acopio") {
        doc.setFontSize(13);
        doc.setTextColor("red");
        doc.text(165, 67, " " + this.auxfecharetiro);
      }
      doc.setTextColor(0, 0, 0);
      doc.setLineWidth(0.8);
      doc.line(10, 75, 200, 75);
      doc.setLineWidth(0.6);
      doc.line(10, 78, 200, 78);
      doc.setFontSize(9);
      doc.text(10, 85, "Cantidad ");
      doc.setLineWidth(0.6);
      doc.text(30, 85, "Articulo ");
      doc.setLineWidth(0.6);
      doc.text(100, 85, "Precio U.");
      doc.setLineWidth(0.6);
      doc.text(170, 85, "Sub Total ");

      doc.setLineWidth(0.6);
      doc.line(10, 88, 200, 88);

      var contador = 95;

      this.productos.forEach((element) => {
        console.log(element);
        //    doc.setFontSize(9);
        doc.text(10, contador, "  " + element.cantidad);
        doc.setLineWidth(0.6);
        doc.text(30, contador, " " + element.nombre);
        doc.setLineWidth(0.6);
        if (this.auxtipoventa == "cta cte") {
          doc.text(100, contador, "");
          doc.setLineWidth(0.6);
          doc.text(170, contador, "");
        } else {
          doc.text(100, contador, "" + element.precio);
          doc.setLineWidth(0.6);
          doc.text(170, contador, "" + element.sub_total);
        }

        contador = contador + 10;
      });
      if (this.auxtipoventa == "cta cte") {
        contador = contador + 20;
        doc.setDrawColor(255, 0, 0);
        doc.setLineWidth(0.4);
        doc.line(10, contador - 10, 200, contador - 10);
        doc.text(90, contador - 7, " Articulos Pagados");
        this.productoscta.forEach((element) => {
          console.log(element);
          //    doc.setFontSize(9);
          doc.text(10, contador, "  " + element.cantidad);
          doc.setLineWidth(0.6);
          doc.text(30, contador, " " + element.nombre);
          doc.setLineWidth(0.6);

          doc.text(100, contador, "" + element.precio);
          doc.setLineWidth(0.6);
          doc.text(170, contador, "" + element.sub_total);

          contador = contador + 10;
        });
      }
      contador = contador + 10;

      doc.setFontSize(14);
      if (this.presupuesto != true) {
        if (this.auxtipoventa == "cta cte") {
          doc.text(10, contador, "Total $  " + Math.round(this.montocta));
          contador = contador + 10;
        } else {
          doc.text(10, contador, "Total $  " + Math.round(this.monto));
          contador = contador + 10;
        }

        doc.text(10, contador, "Entrego $  " + this.efectivo);
        contador = contador + 10;
        doc.text(10, contador, "Su vuelto $  " + Math.round(this.fin));
      } else {
        doc.text(
          10,
          contador,
          "Los precios estan sujetos a cambio sin previo aviso"
        );
      }

      doc.save(pdfName + ".pdf");
    },
    limpiar() {
      this.moneda=[];
      this.montocta = 0;
      this.productos = [];
      this.fin = 0;
      this.monto = 0;
      this.efectivo = 0;
      this.identificador = "";
      this.customer = [];
      this.tipopago = 'Efectivo';
      this.strarticulos = "";
      this.value = "";
      this.recargaarticulos();
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

    guardarcta() {
      let cont = 0;
      this.productoscta = [];
      this.montocta = 0;
      this.selected.forEach((element) => {
        this.productoscta.push({
          pk: element.pk,
          id: cont,
          nombre: element.nombre,
          precio: Math.round(element.precio),
          cantidad: element.cantidad,
          sub_total: Math.round(element.cantidad * element.precio),
        });
        cont++;
        this.montocta += Math.round(element.cantidad * element.precio);
      });

      this.productoscta.forEach((element) => {
        console.log(element);
      });

      //  console.log(this.montocta);
    },
    guardar(cont) {
      this.items.forEach((element) => {
        if (this.identificador == element.id) {
          if (element.cantidad == "0") {
            confirm("No tiene stock disonible de " + element.nombre);
          } else if (
            this.totales == parseInt(element.cantidad) ||
            this.totales < parseInt(element.cantidad)
          ) {
            if (
              parseInt(element.stockminimo) == parseInt(element.cantidad) ||
              parseInt(element.stockminimo) > parseInt(element.cantidad)
            ) {
              confirm("Debe comprar el articulo " + element.nombre);
            }
            this.productos.push({
              pk: element.id,
              id: cont,
              nombre: element.nombre,
              precio: Math.round(element.precio),
              cantidad: this.totales,
              sub_total: Math.round(this.totales * element.precio),
            });
            // this.strarticulos +=
            //   this.totales +
            //   "  " +
            //   element.nombre +
            //   "  " +
            //   element.precio +
            //   "-" +
            //   "\n";
            cont++;
            this.monto += Math.round(this.totales * element.precio);
          } else if (parseInt(element.cantidad) < this.totales) {
            confirm(
              "La cantidad requerida excede lo disponible  " +
                element.nombre +
                "  " +
                element.cantidad +
                " unidades en stock"
            );
          }
        }
      });

      this.recargaarticulos();
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
