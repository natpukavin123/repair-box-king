@extends('layouts.app')
@section('page-title', 'Reports')
@section('content')
<style>
.step-wrap { background:#fff; border-radius:1.2rem; border:1px solid #e2e8f0; margin-bottom:1rem; overflow:hidden; transition:all 0.2s; }
.step-head { display:flex; align-items:center; gap:0.75rem; padding:0.9rem 1.25rem; }
.step-num { width:1.8rem; height:1.8rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; flex-shrink:0; }
.step-num.done { background:#22c55e; color:#fff; }
.step-num.active { background:#6366f1; color:#fff; }
.step-num.idle { background:#f1f5f9; color:#94a3b8; }
.step-body { padding:0 1.25rem 1.25rem; }
.mod-btn { display:flex; flex-direction:column; align-items:center; gap:0.3rem; padding:0.85rem 1.1rem; border-radius:0.9rem; border:2px solid #e2e8f0; cursor:pointer; transition:all 0.15s; background:#fafafa; min-width:76px; font-size:0.75rem; font-weight:600; color:#64748b; }
.mod-btn:hover { border-color:#6366f1; color:#6366f1; }
.mod-btn.active { border-color:var(--mc); color:var(--mc); background:#fff; box-shadow:0 4px 14px -4px rgba(0,0,0,0.12); transform:translateY(-1px); }
.mod-btn svg { width:1.4rem; height:1.4rem; }
.preset-pill { padding:0.35rem 0.85rem; border-radius:99px; border:1px solid #e2e8f0; background:#f8fafc; font-size:0.78rem; font-weight:600; color:#64748b; cursor:pointer; transition:all 0.15s; }
.preset-pill:hover { background:#6366f1; color:#fff; border-color:#6366f1; }
.preset-pill.active { background:#6366f1; color:#fff; border-color:#6366f1; }
.stat-card { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; padding:1rem 1.25rem; }
.stat-card .val { font-size:1.35rem; font-weight:800; line-height:1; margin-top:0.25rem; }
.stat-card .lbl { font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8; }
.cust-badge { font-size:0.62rem; padding:0.12rem 0.45rem; border-radius:99px; font-weight:700; }
.new-c { background:#dcfce7; color:#15803d; }
.rep-c { background:#dbeafe; color:#1d4ed8; }
</style>

<div x-data="reportsPage()" x-init="init()">

  <div class="page-header-inline mb-4">
    <div class="page-header-inline-copy">
      <h2 class="page-header-inline-title">📊 Reports</h2>
      <p class="page-header-inline-description">Choose module → set date range → generate report.</p>
    </div>
  </div>

  {{-- STEP 1: Module --}}
  <div class="step-wrap">
    <div class="step-head">
      <span class="step-num" :class="module ? 'done' : 'active'">
        <span x-show="!module">1</span>
        <svg x-show="module" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
      </span>
      <div class="flex-1">
        <div class="font-semibold text-gray-800 text-sm">Select Module</div>
        <div x-show="module" class="text-xs text-gray-400 mt-0.5" x-text="modules.find(m=>m.key===module)?.label + ' selected'"></div>
      </div>
      <button x-show="module" @click="resetModule()" class="text-xs text-gray-400 hover:text-red-500 transition">Change</button>
    </div>
    <div class="step-body">
      <div class="flex flex-wrap gap-2">
        <template x-for="m in modules" :key="m.key">
          <button @click="pickModule(m.key)" class="mod-btn" :class="module===m.key?'active':''" :style="module===m.key?`--mc:${m.color}`:''">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="module===m.key?`color:${m.color}`:''">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="m.icon"/>
            </svg>
            <span x-text="m.label"></span>
          </button>
        </template>
      </div>
    </div>
  </div>

  {{-- STEP 2: Date Range --}}
  <div class="step-wrap" :style="!module ? 'opacity:0.45;pointer-events:none' : ''">
    <div class="step-head">
      <span class="step-num" :class="!module ? 'idle' : (report ? 'done' : 'active')">
        <span x-show="!report || !module">2</span>
        <svg x-show="report" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
      </span>
      <div class="flex-1">
        <div class="font-semibold text-gray-800 text-sm">Select Date Range</div>
        <div x-show="from&&to" class="text-xs text-gray-400 mt-0.5" x-text="dateLabel()"></div>
      </div>
    </div>
    <div class="step-body">

      {{-- Mode tabs --}}
      <div class="flex gap-1 mb-4 bg-gray-100 rounded-xl p-1 w-fit">
        <button @click="dateMode='month'" class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-all" :class="dateMode==='month'?'bg-white text-gray-800 shadow':'text-gray-500 hover:text-gray-700'">Month</button>
        <button @click="dateMode='year'"  class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-all" :class="dateMode==='year'?'bg-white text-gray-800 shadow':'text-gray-500 hover:text-gray-700'">Year</button>
        <button @click="dateMode='custom'" class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-all" :class="dateMode==='custom'?'bg-white text-gray-800 shadow':'text-gray-500 hover:text-gray-700'">Custom</button>
      </div>

      {{-- Month picker --}}
      <div x-show="dateMode==='month'" class="flex flex-wrap gap-2 mb-4">
        <template x-for="m in pastMonths()" :key="m.key">
          <button @click="pickMonth(m)" class="preset-pill" :class="preset===m.key?'active':''">
            <span x-text="m.label"></span>
          </button>
        </template>
      </div>

      {{-- Year picker --}}
      <div x-show="dateMode==='year'" class="flex flex-wrap gap-2 mb-4">
        <template x-for="y in pastYears()" :key="y.key">
          <button @click="pickYear(y)" class="preset-pill" :class="preset===y.key?'active':''">
            <span x-text="y.label"></span>
          </button>
        </template>
      </div>

      {{-- Custom picker --}}
      <div x-show="dateMode==='custom'" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
          <input x-model="from" @change="preset='custom'" type="date" class="form-input-custom text-sm">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
          <input x-model="to" @change="preset='custom'" type="date" class="form-input-custom text-sm">
        </div>
      </div>

      {{-- Generate --}}
      <div class="flex items-center gap-3">
        <button @click="fetchReport()" :disabled="loading||!from||!to" class="btn-primary px-6 py-2 text-sm">
          <span x-show="loading" class="spinner mr-1"></span>
          <span x-text="loading?'Generating…':'Generate Report'"></span>
        </button>
        <button x-show="report" @click="report=null;tableSearch=''" class="text-xs text-gray-400 hover:text-red-500 transition">Clear results</button>
      </div>

    </div>
  </div>

  {{-- STEP 3: Results --}}
  <div x-show="report" x-cloak>
    <div class="step-wrap mb-4">
      <div class="step-head">
        <span class="step-num done"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></span>
        <div class="flex-1">
          <div class="font-semibold text-gray-800 text-sm">Report Results</div>
          <div class="text-xs text-gray-400 mt-0.5" x-text="modules.find(m=>m.key===module)?.label + ' · ' + from + ' to ' + to"></div>
        </div>
      </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-4">
      <template x-for="s in summaryCards()" :key="s.label">
        <div class="stat-card">
          <div class="lbl" x-text="s.label"></div>
          <div class="val" :style="`color:${s.color}`" x-text="s.value"></div>
        </div>
      </template>
    </div>

    {{-- Customer Insight --}}
    <div x-show="report?.summary?.new_customers !== undefined" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
      <div class="stat-card" style="border-color:#bbf7d0"><div class="lbl" style="color:#15803d">New Customers</div><div class="val" style="color:#15803d" x-text="report?.summary?.new_customers??0"></div></div>
      <div class="stat-card" style="border-color:#bfdbfe"><div class="lbl" style="color:#1d4ed8">Repeat Customers</div><div class="val" style="color:#1d4ed8" x-text="report?.summary?.repeat_customers??0"></div></div>
      <div class="stat-card"><div class="lbl">Walk-in</div><div class="val" style="color:#64748b" x-text="report?.summary?.walk_in_count??0"></div></div>
      <div class="stat-card" style="border-color:#fde68a"><div class="lbl" style="color:#b45309">Total Records</div><div class="val" style="color:#b45309" x-text="report?.list?.length??0"></div></div>
    </div>

    {{-- Breakdown --}}
    <div x-show="breakdownItems().length>0" class="card mb-4">
      <div class="card-header py-2"><h3 class="text-sm font-semibold text-gray-700" x-text="breakdownTitle()"></h3></div>
      <div class="card-body py-2">
        <div class="flex flex-wrap gap-2">
          <template x-for="b in breakdownItems()" :key="b.label">
            <div class="flex items-center gap-1.5 bg-gray-50 rounded-lg px-3 py-1.5 text-sm">
              <span class="font-semibold text-gray-700 capitalize" x-text="b.label"></span>
              <span class="text-gray-400">·</span>
              <span class="font-bold text-gray-800" x-text="b.count"></span>
              <span x-show="b.total" class="text-primary-600 font-semibold" x-text="' ₹'+Number(b.total).toLocaleString('en-IN')"></span>
            </div>
          </template>
        </div>
      </div>
    </div>

    {{-- Table --}}
    <div class="card">
      <div class="card-header flex items-center justify-between py-2">
        <h3 class="text-sm font-semibold text-gray-700">Transactions (<span x-text="filteredList().length"></span>)</h3>
        <input x-model="tableSearch" type="text" placeholder="Filter…" class="form-input-custom text-xs py-1.5 px-3 w-40">
      </div>
      <div class="overflow-x-auto">
        <table class="data-table w-full text-sm">
          <thead><tr>
            <template x-for="h in tableHeaders()" :key="h">
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap" x-text="h"></th>
            </template>
          </tr></thead>
          <tbody>
            <template x-for="(row,i) in filteredList()" :key="i">
              <tr class="border-t border-gray-100 hover:bg-gray-50/60">
                <template x-for="(cell,j) in tableRow(row)" :key="j">
                  <td class="px-3 py-2 whitespace-nowrap" x-html="cell"></td>
                </template>
              </tr>
            </template>
            <tr x-show="filteredList().length===0">
              <td :colspan="tableHeaders().length" class="text-center py-10 text-gray-400">No records found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function reportsPage(){
  return {
    module:'', preset:'this_month', from:'', to:'',
    loading:false, report:null, tableSearch:'',
    modules:[
      {key:'repairs',   label:'Repairs',  color:'#f97316', icon:'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'},
      {key:'sales',     label:'Sales',    color:'#22c55e', icon:'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z'},
      {key:'recharges', label:'Recharge', color:'#8b5cf6', icon:'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'},
      {key:'expenses',  label:'Expenses', color:'#ef4444', icon:'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'},
      {key:'po',        label:'PO',       color:'#f59e0b', icon:'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'},
    ],
    init(){ this.dateMode='month'; this.from=''; this.to=''; this.preset=''; },
    pickModule(k){ this.module=k; this.report=null; this.tableSearch=''; this.preset=''; },
    resetModule(){ this.module=''; this.report=null; this.tableSearch=''; this.preset=''; },

    // Past months of current year (Jan → last complete month)
    pastMonths(){
      const n=new Date(), yr=n.getFullYear(), cur=n.getMonth(); // 0-indexed
      const names=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      const out=[];
      for(let m=0;m<cur;m++){
        const key=`${yr}-${String(m+1).padStart(2,'0')}`;
        const last=new Date(yr,m+1,0).getDate();
        out.push({key, label:names[m]+' '+yr, from:`${yr}-${String(m+1).padStart(2,'0')}-01`, to:`${yr}-${String(m+1).padStart(2,'0')}-${String(last).padStart(2,'0')}`});
      }
      return out.reverse(); // most recent first
    },

    // Past 4 years
    pastYears(){
      const yr=new Date().getFullYear();
      return [0,1,2,3].map(i=>({key:`yr-${yr-i}`, label:String(yr-i), from:`${yr-i}-01-01`, to:`${yr-i}-12-31`}));
    },

    pickMonth(m){ this.preset=m.key; this.from=m.from; this.to=m.to; },
    pickYear(y){ this.preset=y.key; this.from=y.from; this.to=y.to; },

    dateLabel(){
      if(!this.from||!this.to) return '';
      const names=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      const f=new Date(this.from), t=new Date(this.to);
      if(this.dateMode==='month') return names[f.getMonth()]+' '+f.getFullYear();
      if(this.dateMode==='year') return 'Year '+f.getFullYear();
      return this.from+' → '+this.to;
    },

    async fetchReport(){
      if(!this.from||!this.to){RepairBox.toast('Select date range','error');return;}
      this.loading=true;this.report=null;this.tableSearch='';
      const r=await RepairBox.ajax(`/admin/reports/${this.module}?from=${this.from}&to=${this.to}`);
      this.loading=false;
      if(r.data) this.report=r.data;
    },
    fmt(n){return '₹'+Number(n||0).toLocaleString('en-IN',{minimumFractionDigits:0,maximumFractionDigits:0});},
    summaryCards(){
      const s=this.report?.summary; if(!s) return [];
      if(this.module==='repairs') return [
        {label:'Total Tickets',value:s.total_tickets,color:'#f97316'},
        {label:'Revenue',value:this.fmt(s.total_revenue),color:'#22c55e'},
        {label:'Refunded',value:this.fmt(s.total_refunded),color:'#ef4444'},
        {label:'Net Revenue',value:this.fmt(s.net_revenue),color:'#6366f1'},
        {label:'Avg Ticket',value:this.fmt(s.avg_ticket_value),color:'#8b5cf6'},
      ];
      if(this.module==='sales') return [
        {label:'Invoices',value:s.total_invoices,color:'#22c55e'},
        {label:'Total Sales',value:this.fmt(s.total_sales),color:'#22c55e'},
        {label:'Discounts',value:this.fmt(s.total_discount),color:'#ef4444'},
        {label:'Items Sold',value:s.total_items_sold,color:'#6366f1'},
        {label:'Avg Sale',value:this.fmt(s.avg_sale),color:'#8b5cf6'},
      ];
      if(this.module==='recharges') return [
        {label:'Recharges',value:s.total_recharges,color:'#8b5cf6'},
        {label:'Amount',value:this.fmt(s.total_amount),color:'#8b5cf6'},
        {label:'Commission',value:this.fmt(s.total_commission),color:'#22c55e'},
      ];
      if(this.module==='expenses') return [
        {label:'Expenses',value:s.total_expenses,color:'#ef4444'},
        {label:'Total Amount',value:this.fmt(s.total_amount),color:'#ef4444'},
        {label:'Avg Expense',value:this.fmt(s.avg_expense),color:'#f97316'},
      ];
      if(this.module==='po') return [
        {label:'PO Orders',value:s.total_orders,color:'#f59e0b'},
        {label:'Total Amount',value:this.fmt(s.total_amount),color:'#f59e0b'},
        {label:'Avg Order',value:this.fmt(s.avg_order),color:'#f97316'},
      ];
      return [];
    },
    breakdownTitle(){
      if(this.module==='repairs') return 'Status Breakdown';
      if(this.module==='expenses') return 'By Category';
      if(this.module==='po') return 'By Status';
      return '';
    },
    breakdownItems(){
      const s=this.report?.summary; if(!s) return [];
      if(this.module==='repairs'&&s.status_counts) return Object.entries(s.status_counts).map(([k,v])=>({label:k.replace(/_/g,' '),count:v}));
      if(this.module==='expenses'&&s.by_category) return Object.entries(s.by_category).map(([k,v])=>({label:k,count:v.count,total:v.total}));
      if(this.module==='po'&&s.by_status) return Object.entries(s.by_status).map(([k,v])=>({label:k,count:v.count,total:v.total}));
      return [];
    },
    tableHeaders(){
      if(this.module==='repairs')   return ['Date','Ticket','Customer','Mobile','Device','Status','Estimated','Final','Paid','Balance'];
      if(this.module==='sales')     return ['Date','Invoice','Customer','Mobile','Items','Amount','Discount','Status'];
      if(this.module==='recharges') return ['Date','Customer','Mobile','Plan','Amount','Commission','Method','Status'];
      if(this.module==='expenses')  return ['Date','Category','Description','Amount','Method'];
      if(this.module==='po')        return ['Date','Invoice No','Supplier','Amount','Status','Notes'];
      return [];
    },
    cb(row){
      if(row.is_new_cust===true) return '<span class="cust-badge new-c ml-1">New</span>';
      if(row.is_new_cust===false&&row.customer!=='Walk-in') return '<span class="cust-badge rep-c ml-1">Repeat</span>';
      return '';
    },
    sb(st){
      const m={received:'bg-blue-100 text-blue-700',in_progress:'bg-amber-100 text-amber-700',completed:'bg-green-100 text-green-700',closed:'bg-gray-100 text-gray-600',cancelled:'bg-red-100 text-red-600',paid:'bg-green-100 text-green-700',partial:'bg-amber-100 text-amber-700',unpaid:'bg-red-100 text-red-600',pending:'bg-amber-100 text-amber-700',active:'bg-green-100 text-green-700'};
      const c=m[st]||'bg-gray-100 text-gray-600';
      return `<span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full ${c} capitalize">${(st||'').replace(/_/g,' ')}</span>`;
    },
    tableRow(row){
      if(this.module==='repairs') return [row.date,`<span class="font-semibold text-orange-600 text-xs">${row.ticket}</span>${row.tracking?`<br><span class="text-[10px] text-gray-400">${row.tracking}</span>`:''}`,`${row.customer}${this.cb(row)}`,row.mobile||'—',`<span class="text-xs text-gray-600">${row.device||'—'}</span>`,this.sb(row.status),this.fmt(row.estimated),this.fmt(row.final),this.fmt(row.paid),`<span class="${row.balance>0?'text-red-600 font-semibold':'text-green-600'}">${this.fmt(row.balance)}</span>`];
      if(this.module==='sales') return [row.date,`<span class="font-semibold text-green-700 text-xs">${row.invoice_no}</span>`,`${row.customer}${this.cb(row)}`,row.mobile||'—',row.items,`<span class="font-semibold">${this.fmt(row.amount)}</span>`,row.discount>0?`<span class="text-red-500">-${this.fmt(row.discount)}</span>`:'—',this.sb(row.status)];
      if(this.module==='recharges') return [row.date,row.customer,row.mobile,`<span class="text-purple-700 font-medium text-xs">${row.plan||'—'}</span>`,this.fmt(row.amount),row.commission>0?`<span class="text-green-600">+${this.fmt(row.commission)}</span>`:'—',row.method||'—',this.sb(row.status)];
      if(this.module==='expenses') return [row.date,`<span class="font-medium text-red-600 text-xs">${row.category}</span>`,`<span class="text-gray-500 text-xs">${row.description||'—'}</span>`,`<span class="font-semibold text-red-700">${this.fmt(row.amount)}</span>`,row.method||'—'];
      if(this.module==='po') return [row.date,`<span class="font-semibold text-amber-700 text-xs">${row.invoice_no||'—'}</span>`,row.supplier,`<span class="font-semibold">${this.fmt(row.amount)}</span>`,this.sb(row.status),`<span class="text-gray-400 text-xs">${row.notes||'—'}</span>`];
      return [];
    },
    filteredList(){
      const l=this.report?.list||[];
      if(!this.tableSearch.trim()) return l;
      const q=this.tableSearch.toLowerCase();
      return l.filter(r=>JSON.stringify(r).toLowerCase().includes(q));
    },
  };
}
</script>
@endpush
