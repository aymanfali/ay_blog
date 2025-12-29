<script setup lang="ts">
import type { ColumnDef } from '@tanstack/vue-table'
import DataTable from '@/components/dashboard/data-table/DataTable.vue'
import type { ColumnMeta } from '@/components/dashboard/data-table/types'
import type { User } from '@/types/user'
import { h, ref, onMounted, type Ref, computed } from 'vue'
import StatusTag from '@/components/dashboard/data-table/StatusTag.vue'
import { STATUS_CONFIG } from '@/config/status.config'
import api from '@/api/axios'
import { API } from '@/api/endpoints'

import { useI18n } from 'vue-i18n'
import { Button } from '@/components/ui/button'
import { Edit, Plus, Trash } from 'lucide-vue-next'
import EntityModal from '@/components/modals/EntityModal.vue'
import ConfirmDeleteModal from '@/components/modals/ConfirmDeleteModal.vue'
import { Input } from '@/components/ui/input'
import PasswordInput from '@/components/inputs/PasswordInput.vue'
import { Switch } from '@/components/ui/switch'

const { d } = useI18n()

const isLoading = ref(true)
const users: Ref<User[]> = ref([])

const selectedItem = ref<User | null>(null)

const showDelete = ref(false)
const showEdit = ref(false)
const showCreate = ref(false)

/* Forms */
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  status: true,
})

const createForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  status: true,
})

/* Pagination */
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
})

const columns: ColumnDef<User>[] = [
  {
    accessorKey: 'name',
    header: 'Name',
    cell: ({ row }) => row.getValue('name'),
    meta: { filter: { type: 'text', placeholder: 'Search name…' } } as ColumnMeta,
  },
  {
    accessorKey: 'email',
    header: 'Email',
    cell: ({ row }) => row.getValue('email'),
    meta: { filter: { type: 'text', placeholder: 'Search email…' } } as ColumnMeta,
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: ({ row }) => h(StatusTag, { value: String(row.getValue('status')) }),
    meta: {
      filter: {
        type: 'select',
        options: Object.entries(STATUS_CONFIG).map(([value, cfg]) => ({
          value,
          label: cfg.label,
        })),
      },
    } as ColumnMeta,
    filterFn: (row, columnId, value) => {
      if (!value) return true
      return row.getValue(columnId) === value
    },
  },
  {
    accessorKey: 'created_at',
    header: 'Created At',
    cell: ({ row }) => d(new Date(row.getValue('created_at')), 'long'),
  },
  {
    accessorKey: 'updated_at',
    header: 'Updated At',
    cell: ({ row }) => d(new Date(row.getValue('updated_at')), 'long'),
  },
]

/* API */
const fetchUsers = async (page = 1) => {
  isLoading.value = true
  try {
    const res = await api.get(API.USERS.index, { params: { page } })

    users.value = res.data.data.data ?? []

    const pag = res.data.data
    pagination.value = {
      current_page: pag.current_page,
      last_page: pag.last_page,
      per_page: pag.per_page,
      total: pag.total,
    }
  } catch (err) {
    console.error('Failed to fetch users:', err)
    users.value = []
  } finally {
    isLoading.value = false
  }
}

/* -------------------------------------------------------------------------- */
/* Actions */
const openCreate = () => {
  createForm.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    status: true,
  }

  showCreate.value = true
}

const openEdit = (item: User) => {
  selectedItem.value = item

  form.value = {
    name: item.name ?? '',
    email: item.email ?? '',
    password: '',
    password_confirmation: '',
    status: item.status === 'active',
  }

  showEdit.value = true
}

const openDelete = (item: User) => {
  selectedItem.value = item
  showDelete.value = true
}

const deleteDescription = computed(() =>
  selectedItem.value
    ? `Are you sure you want to delete “${selectedItem.value.name}”? This action cannot be undone.`
    : '',
)

const handleDelete = async () => {
  if (!selectedItem.value) return
  await api.delete(API.USERS.destroy(selectedItem.value.id))
  showDelete.value = false
  await fetchUsers()
}

const handleUpdate = async () => {
  if (!selectedItem.value) return

  if (form.value.password && form.value.password !== form.value.password_confirmation) {
    alert('Passwords do not match')
    return
  }

  const payload: any = {
    name: form.value.name,
    email: form.value.email,
    status: form.value.status ? 'active' : 'inactive',
  }

  if (form.value.password) {
    payload.password = form.value.password
  }

  await api.put(API.USERS.update(selectedItem.value.id), payload)

  showEdit.value = false
  await fetchUsers()
}

const handleCreate = async () => {
  if (!createForm.value.name || !createForm.value.email || !createForm.value.password) {
    alert('Please fill all required fields')
    return
  }

  if (createForm.value.password !== createForm.value.password_confirmation) {
    alert('Passwords do not match')
    return
  }

  const payload = {
    name: createForm.value.name,
    email: createForm.value.email,
    password: createForm.value.password,
    password_confirmation: createForm.value.password_confirmation,
    status: createForm.value.status ? 'active' : 'inactive',
  }
  console.log(payload)

  await api.post(API.USERS.index, payload)

  showCreate.value = false
  await fetchUsers()
}

/* -------------------------------------------------------------------------- */

onMounted(fetchUsers)
</script>

<template>
  <div class="flex justify-end mb-4">
    <Button variant="outline" @click="openCreate" class="flex items-center gap-2">
      <Plus /> Add User
    </Button>
  </div>

  <DataTable :columns="columns" :data="users" :loading="isLoading">
    <template #row-actions="{ row } = {}">
      <div v-if="row" class="flex gap-2">
        <Button size="sm" variant="outline" @click="openEdit(row)">
          <Edit />
        </Button>
        <Button size="sm" variant="destructive" @click="openDelete(row)">
          <Trash />
        </Button>
      </div>
    </template>
  </DataTable>

  <ConfirmDeleteModal
    :open="showDelete"
    title="Delete record"
    :description="deleteDescription"
    @close="showDelete = false"
    @confirm="handleDelete"
  />

  <!-- Create -->
  <EntityModal
    :open="showCreate"
    title="Create User"
    @close="showCreate = false"
    @submit="handleCreate"
  >
    <div class="flex gap-2 m-2">
      <Input v-model="createForm.name" placeholder="Name" />
      <Input v-model="createForm.email" placeholder="Email" />
    </div>

    <div class="flex gap-2 m-2">
      <PasswordInput v-model="createForm.password" placeholder="Password" />
      <PasswordInput v-model="createForm.password_confirmation" placeholder="Confirm Password" />
    </div>

    <div class="flex items-center gap-3 m-2">
      <Switch
        v-model="createForm.status"
        activeText="Active"
        inactiveText="Inactive"
        label="Status"
      />
    </div>
  </EntityModal>

  <!-- Edit -->
  <EntityModal :open="showEdit" title="Edit User" @close="showEdit = false" @submit="handleUpdate">
    <div class="flex gap-2 m-2">
      <Input v-model="form.name" placeholder="Name" />
      <Input v-model="form.email" placeholder="Email" />
    </div>

    <div class="flex gap-2 m-2">
      <PasswordInput v-model="form.password" placeholder="Password" />
      <PasswordInput v-model="form.password_confirmation" placeholder="Confirm Password" />
    </div>

    <div class="flex items-center gap-3 m-2">
      <Switch v-model="form.status" activeText="Active" inactiveText="Inactive" label="Status" />
    </div>
  </EntityModal>
</template>
