<template>
  <v-app>
    <v-card class="mx-auto" width="1150">
      <br />
      <v-label><h1 style="margin-left: 10px">Informe</h1></v-label>
      <br />
      <v-label
        ><h3 style="margin-left: 10px">
          Filtrar por Apellido ó Fecha ó Tipo de Pago ó Ambos
        </h3></v-label
      >
      <v-row>
        <v-col md="3" style="margin-top: 30px">
          <v-text-field
            label="Ingrese numero de remito"
            
            v-model="numeroremito"
            outlined
          ></v-text-field>
        </v-col>
        <v-col md="3">
          <v-autocomplete
            style="margin-left: 10px; margin-top: 30px"
            v-model="value"
            :items="clientes"
            item-text="apellido"
            item-value="apellido"
            dense
            filled
            label="Buscar por apellido"
          ></v-autocomplete>
        </v-col>
        <v-col md="3" style="margin-top: 30px">
          <date-picker
            label="Fecha"
            v-model="time1"
            valueType="format"
          ></date-picker
        ></v-col>
        
        <v-col md="3" style="margin-top: 30px">
          <v-btn color="green" @click="filtar()" outlined>Buscar</v-btn>
          <v-btn
            style="margin-left: 30px"
            color="red"
            @click="limpiar()"
            outlined
            >Limpiar</v-btn
          >
        </v-col>
      </v-row>
      <v-label v-if="totalventa != 0"
        ><h3 style="margin-left: 10px">Total: {{ totalventa }}</h3></v-label
      >
      <v-col md="12">
        <v-btn outlined color="green" @click="tirarcaja()">Caja</v-btn>
        <br /><br />
        <v-label style="margin-left: 150px" v-if="Efectivo != 0"
          >Efectivo $:{{ Efectivo }}___ Cheque: {{ Cheque }}__ Cta. DNI:
          {{ CtaDNI }}__ Mercado Pago: {{ MercadoPago }}__ Transferencia:
          {{ Transferencia }}__ Targeta: {{ Targeta }}</v-label
        >
      </v-col>

      <v-data-table
        :headers="headers"
        :items="tableventas"
        :items-per-page="5"
        class="elevation-1"
      >
        <template v-slot:[`item.actions`]="{ item }">
          <v-icon small class="mr-2" @click="amplia(item)"> mdi-pencil </v-icon>
          <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
        </template>
      </v-data-table>
      <v-dialog v-model="dialog" width="500px">
        <v-card height="720px">
          <br />
          <div>
            <div style="margin-left: 350px">
              <v-label>Fecha: {{ detalle.fecha }} <br /> </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label>Cliente: {{ detalle.apellido }} <br /> </v-label>
            </div>
            <br />
            <div style="margin-left: 50px" v-if="detalle.tipoventa == 'Acopio'">
              <v-label
                >Estado: {{ detalle.tipoventa }} <br />
                Fecha Estimada de retiro {{ detalle.fecharetiro }} <br />
              </v-label>
            </div>
            <div v-else style="margin-left: 50px">
              <v-label
                >Estado: {{ detalle.tipoventa }} <br />
                Fecha de salida: {{ detalle.fechasalida }}
              </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label>Articulos <br /> </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label v-for="art in strarts" :key="art">
                {{ art }} <br />
              </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label>Tipo de Pago: {{ detalle.tipopago }} <br /> </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label>Forma/s de pago <br /> </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label v-for="mixto in datosm" :key="mixto">
                {{ mixto.tipo }} {{ mixto.pesos }} <br />
              </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label
                >Total: $ <b> {{ detalle.monto }} </b> <br />
              </v-label>
            </div>
            <br />
            <div style="margin-left: 50px" v-if="detalle.tipoventa == 'Acopio'">
              <v-icon @click="dialogacopio=true" color="red">mdi-search-web</v-icon>
              <v-col md="10"><v-textarea outlined color="green" label="Detalle" v-model="detalleacopio" ></v-textarea></v-col>
              
              <v-btn style="margin-left: 10px" outlined color="green" @click="actualizaestadoacopio(detalle.id)"
                >Guardar</v-btn
              >
              <v-btn style="margin-left: 200px" outlined color="red" @click="actualizaestado(detalle.id)"
                >Retirado</v-btn
              >
            </div>
          </div>
        </v-card>
      </v-dialog>
      <v-dialog v-model="dialogacopio" width="500px">
        <v-card height="600px">

           <div style="margin-left: 50px">
              <v-label v-for="art in dtacopio" :key="art">
                {{ art.detalle }}___ {{art.fecha}}<br />
              </v-label>
            </div>
              
        </v-card>


      </v-dialog>
    </v-card>
  </v-app>
</template>
<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
export default {
  components: { DatePicker },
  data() {
    return {
      dialogacopio:false,
      detalleacopio:'',
      Efectivo: 0,
      Targeta: 0,
      Cheque: 0,
      CtaDNI: 0,
      MercadoPago: 0,
      Transferencia: 0,
      datosmixtos: [],
      datosm: [],
      detalle: [],
      tipopago: null,
      dialog: false,
      time1: null,
      aux: [],
      clientes: [],
      value: null,
      ventas: [],
      totalventa: 0,
      numeroremito: 0,
      dtacopio:[],
      tiposdepago: ["Simple", "Mixto"],
      headers: [
        { text: "ID", value: "id" },
        {
          text: "Apellido y nombre",
          align: "start",
          sortable: false,
          value: "apellido",
        },
        { text: "Fecha", value: "fecha" },
        { text: "Monto", value: "monto" },
        { text: "Tipo de pago", value: "tipopago" },
        { text: "Estado", value: "tipoventa" },
        { text: "Acciones", value: "actions", sortable: false },
      ],
      ventas: [],
      tableventas: [],
      strarts: [],
    };
  },
  created() {
    this.cargaclientes();
    this.cargaventas();
    this.cargamixtos();
    this.cargadetalleacopio()
  },
  methods: {
     actualizaestadoacopio(fk) {
      // alert(this.montodeuda + "" + fk);
      var formdata = new FormData();

      formdata.append("id", "");
      
      formdata.append("detalle", this.detalleacopio);
      formdata.append("fkventa", fk);
      formdata.append("fecha", new Date().toISOString().substr(0, 10));

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
        this.dialog= false
       
      });
    },
    cargamixtos() {
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/mixto.php"
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        this.datosmixtos = data;

        console.log(this.datosmixtos);
      });
    },

    actualizaestado(pk) {
    
      var formdata = new FormData();

      var requestOptions = {
        method: "PUT",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/ventas.php?id=" +
          pk +
          "&tipoventa=" +
          "Retirado" +
          "&fechasalida=" +
          new Date().toISOString().substr(0, 10) +
          "",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .catch((error) => console.log("error", error));
    },
    deleteItem(item) {
      //   console.log(item);
      const index = this.tableventas.indexOf(item);

      var option = confirm("Desea eliminar esta venta! \n  OK or Cancelar.");
      if (option) {
        var formdata = new FormData();

        var requestOptions = {
          method: "DELETE",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/ventas.php?id=" + item.id,
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result), this.cargaventas())

          .catch((error) => console.log("error", error));
      }
    },
    amplia(arts) {
      this.datosm = [];
      console.log(arts.id);
      this.datosmixtos.forEach((element) => {
        if (parseInt(arts.id) == parseInt(element.fkventa)) {
          this.datosm.push(element);
        }
      });

      this.detalle = arts;
      var aux = this.detalle.strarticulos;
      this.strarts = aux.split(["-"]);

      this.dialog = true;
    },
    limpiar() {
      this.tableventas = [];
      this.time1 = null;
      this.value = null;
      this.totalventa = 0;
      this.aux = [];
      this.tipopago = null;
      this.Efectivo = 0;
      this.Targeta = 0;
      this.Cheque = 0;
      this.CtaDNI = 0;
      this.MercadoPago = 0;
      this.Transferencia = 0;
      this.numeroremito=0;
    },
    //  "Efectivo",
    //   "Targeta",
    //   "Cheque",
    //   "Cta. DNI",
    //   "Mercado Pago",
    //   "Transferencia",
    tirarcaja() {
      //  console.log(this.datosmixtos)
      // console.log(this.aux)
      this.aux.forEach((elementaux) => {
        this.datosmixtos.forEach((elementmixtos) => {
          if (elementmixtos.fkventa == elementaux.id) {
            if (elementmixtos.tipo == "Efectivo") {
              this.Efectivo += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Targeta") {
              this.Targeta += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Cheque") {
              this.Cheque += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Cta. DNI") {
              this.CtaDNI += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Mercado Pago") {
              this.MercadoPago += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Transferencia") {
              this.Transferencia += parseInt(elementmixtos.pesos);
            }
            // console.log(elementmixtos.fkventa + "   " + elementaux.id)
          }
        });
      });
    },
    filtar() {
      if (this.value != null && this.time1 != null && this.tipopago != null) {
        // alert(this.value + " " + this.time1 + " " + this.tipopago)
        this.ventas.forEach((element) => {
          if (
            this.value == element.apellido &&
            this.time1 == element.fecha &&
            this.tipopago == element.tipopago &&
            element.tipopago != "Debe"
          ) {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.time1 != null && this.tipopago != null) {
        this.ventas.forEach((element) => {
          if (
            this.time1 == element.fecha &&
            this.tipopago == element.tipopago &&
            element.tipopago != "Debe"
          ) {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      }  else if (this.time1 != null && this.value != null) {
        this.ventas.forEach((element) => {
          if (
            this.time1 == element.fecha &&
            this.value == element.apellido &&
            element.tipopago != "Debe"
          ) {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      }else if (this.time1 != null) {
        this.ventas.forEach((element) => {
          if (this.time1 == element.fecha && element.tipopago != "Debe") {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.value != null) {
        this.ventas.forEach((element) => {
          if (this.value == element.apellido && element.tipopago != "Debe") {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.numeroremito != null) {
        this.ventas.forEach((element) => {
          if (this.numeroremito == element.id && element.tipopago != "Debe") {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else {
        confirm("No se encontraron resultados");
      }
      this.tableventas = this.aux;
      console.log(this.ventas);
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

    cargaventas() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/ventas.php",
        requestOptions
      )
        .then((response) => response.json())
        //.then((result) => (console.log(result)))
        .then((result) => (this.ventas = result))
        .catch((error) => console.log("error", error));
    },
      cargadetalleacopio() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/detalleacopio.php",
        requestOptions
      )
        .then((response) => response.json())
        //.then((result) => (console.log(result)))
        .then((result) => (this.dtacopio = result))
        .catch((error) => console.log("error", error));
    },
  },
};
</script>