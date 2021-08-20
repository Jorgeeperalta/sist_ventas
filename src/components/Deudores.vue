<template>
  <v-app>
    <v-card class="mx-auto" width="1150">
      <br />
      <v-label><h1 style="margin-left: 10px">Deudores</h1></v-label>
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

      <v-data-table
        :headers="headers"
        :items="tableventas"
        :items-per-page="5"
        class="elevation-1"
      >
        <template v-slot:[`item.actions`]="{ item }">
          <v-icon small class="mr-2" @click="amplia(item)"> mdi-pencil </v-icon>
          <v-icon small @click="actualizamontotal(item)"> mdi-plus </v-icon>
        </template>
      </v-data-table>
      <v-dialog v-model="dialog" width="500px">
        <v-card height="700px">
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
            <!-- <div style="margin-left: 50px">
              <v-label> Deuda Inicial <br /> </v-label>
            </div> -->
            <!-- <div style="margin-left: 50px">
              <v-label>Forma/s de pago <br /> </v-label>
            </div> -->
            <div style="margin-left: 50px">
              <v-label v-for="mixto in datosm" :key="mixto">
                Deuda Inicial $ {{ mixto.pesos }} <br />
              </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label v-for="mixto in datodeuda" :key="mixto">
                $ {{ mixto.pago }} _________ Fecha {{ mixto.fecha }} <br />
              </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label
                >Total adeudado: $ <b> {{ detalle.monto }} </b> <br />
              </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label>Monto a abonar: $ <br /></v-label>
              <v-col md="8"
                ><v-text-field
                  style="margin-left: -15px"
                  v-model="montodeuda"
                  label="Monto"
                  outlined
                ></v-text-field>
              </v-col>
              <!-- <v-col md="8"
                ><v-text-field
                  style="margin-left: -15px"
                  v-model="detalledeuda"
                  label="Detalle"
                  outlined
                ></v-text-field>
              </v-col> -->
              <v-btn color="green" outlined @click="actualizardeuda(detalle.id)"
                >Guradar</v-btn
              >
              <v-btn
                color="red"
                style="margin-left: 220px"
                outlined
                @click="cierra()"
                >Cerrar</v-btn
              >
            </div>
            <br />
            <div style="margin-left: 50px" v-if="detalle.tipoventa == 'Acopio'">
              <v-textarea
                outlined
                color="green"
                v-model="detalleacopio"
              ></v-textarea>
              <v-btn
                outlined
                color="green"
                @click="actualizaestadoacopio(detalle.id)"
                >Guardar</v-btn
              >
              <v-btn outlined color="red" @click="actualizaestado(detalle.id)"
                >Retirado</v-btn
              >
            </div>
          </div>
        </v-card>
      </v-dialog>
    </v-card>
     <v-dialog v-model="dialogactualizamonto" max-width="600px">
            <v-card>
              <v-card-title>
                <span class="headline">Actualiza monto de  {{nombredeudor}}</span>
              </v-card-title>

              <v-card-text>
                <v-textarea
                  outlined
                  v-model="porcentaje"
                  label="Ingrese porcentaje"
                ></v-textarea>
                <v-btn outlined color="green" @click="guardaporcentaje()"
                  >Guardar</v-btn
                >
              </v-card-text>
            </v-card>
          </v-dialog>


  </v-app>
</template>
<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
export default {
  components: { DatePicker },
  data() {
    return {
      porcentaje:0,
      nombredeudor:'',
      dialogactualizamonto:false,
      numeroremito: 0,
      datodeuda: [],
      detalledeuda: "",
      montodeuda: 0,
      datosmixtos: [],
      datosm: [],
      datosdeudores: [],
      detalle: [],
      tipopago: null,
      dialog: false,
      time1: null,
      aux: [],
      clientes: [],
      detalleacopio: "",
      value: null,
      ventas: [],
      totalventa: 0,
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
        { text: "Monto Adeudado", value: "monto" },
        { text: "Tipo de pago", value: "tipopago" },
        { text: "Estado", value: "tipoventa" },
        { text: "Acciones", value: "actions", sortable: false },
      ],
      datosdeudor:null,
      ventas: [],
      tableventas: [],
      strarts: [],
    };
  },
  created() {
    this.cargaclientes();
    this.cargaventas();
    this.cargamixtos();
    this.traedeudores();
  },
  methods: {
    guardaporcentaje(){
       
       console.log(this.datosdeudor.id)
        // alert(this.montodeuda + "" + fk);
        var porc= (this.porcentaje / 100) + 1
        var identificador= this.datosdeudor.id

      async function asyncData() {
         const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/porcentajedeuda.php?id=" +
            identificador+
            "&porcentaje=" +
            porc,
          { method: "PUT" }
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        console.log(data);
        alert('Se actualizo con exito!!')

        this.dialogactualizamonto=false;
      
      });
    



    },
    actualizamontotal(deudor){
         this.dialogactualizamonto=true
         this.datosdeudor=deudor;
          this.nombredeudor=this.datosdeudor.apellido
    },
    traedeudores() {
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/actualizardeuda.php"
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        this.datosdeudores = data;

        console.log(this.datosdeudores);
      });
    },
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
      });
    },
    actualizardeuda(fk) {
      // alert(this.montodeuda + "" + fk);
      var formdata = new FormData();

      formdata.append("id", "");
      formdata.append("pago", this.montodeuda);
      formdata.append("detalle", this.detalledeuda);
      formdata.append("fkventa", fk);
      formdata.append("fecha", new Date().toISOString().substr(0, 10));

      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/actualizardeuda.php",
          { method: "POST", body: formdata }
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        console.log(data);
        this.actualizarventa(fk, this.montodeuda);
      });
      this.cargaventas();
    },
    actualizarventa(id, montod) {
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/actualizardeuda.php?id=" +
            id +
            "&monto=" +
            montod,
          { method: "PUT" }
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        console.log(data);
        alert("Almacenado con exito!!");
        this.cargaventas();
        this.dialog = false;
      });
      this.traedeudores();
      this.cargaventas();
    },
    cierra() {
      this.dialog = false;
      this.datodeuda = [];
      this.traedeudores();
      this.cargaventas();
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
      alert(pk);
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
      this.traedeudores();
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
      this.datosdeudores.forEach((element) => {
        if (element.fkventa == arts.id) {
          this.datodeuda.push(element);
        }
      });

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
      this.montodeuda = 0;
      this.tableventas = [];
      this.time1 = null;
      this.value = null;
      this.totalventa = 0;
      this.aux = [];
      this.tipopago = null;
      this.datodeuda = [];
      this.traedeudores();
    },
    filtar() {
      if (this.value != null && this.time1 != null && this.tipopago != null) {
        // alert(this.value + " " + this.time1 + " " + this.tipopago)
        this.ventas.forEach((element) => {
          if (
            this.value == element.apellido &&
            this.time1 == element.fecha &&
            this.tipopago == element.tipopago &&
            element.tipopago == "Debe"
          ) {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.time1 != null && this.value != null) {
        this.ventas.forEach((element) => {
          if (
            this.time1 == element.fecha &&
            this.value == element.apellido &&
            element.tipopago == "Debe"
          ) {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.time1 != null) {
        this.ventas.forEach((element) => {
          if (this.time1 == element.fecha && element.tipopago == "Debe") {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.value != null) {
        this.ventas.forEach((element) => {
          if (this.value == element.apellido && element.tipopago == "Debe") {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.numeroremito != null) {
        this.ventas.forEach((element) => {
          if (this.numeroremito == element.id && element.tipopago == "Debe") {
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
  },
};
</script>