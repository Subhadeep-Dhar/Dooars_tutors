export default function AuthLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <div className="min-h-screen bg-gradient-to-br from-[#8aafdf] via-[#6b9fd4] to-[#003153] flex items-center justify-center p-4">
      {children}
    </div>
  )
}
