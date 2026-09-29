<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

type HttpMethod = 'delete' | 'post' | 'put' | 'patch'

const props = defineProps<{
  url: string
  method?: HttpMethod

  title?: string
  description?: string

  confirmText?: string
  confirmClass?: string
}>()

const form = useForm({})

const submit = () => {
  const method = props.method ?? 'delete'
  form[method](props.url)
}
</script>

<template>
  <AlertDialog>
    <!-- Trigger is fully customizable -->
    <AlertDialogTrigger as-child>
      <slot name="trigger" />
    </AlertDialogTrigger>

    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>
          {{ title ?? 'Are you sure?' }}
        </AlertDialogTitle>

        <AlertDialogDescription>
          {{ description ?? 'This action cannot be undone.' }}
        </AlertDialogDescription>
      </AlertDialogHeader>

      <AlertDialogFooter>
        <AlertDialogCancel>
          Cancel
        </AlertDialogCancel>

        <AlertDialogAction
          :class="confirmClass ?? 'bg-red-600 text-white hover:bg-red-700'"
          :disabled="form.processing"
          @click="submit"
        >
          {{ confirmText ?? 'Confirm' }}
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
